#!/usr/bin/env python3
"""
Set a GitHub Actions secret for a repository using the repo public key.
Usage:
  python scripts/gh_set_secret.py owner repo secret_name secret_value

If secret_value is '@file:path' then the value will be read from the file.
The script reads the GitHub token from the environment variable GITHUB_TOKEN.
"""
import os
import sys
import base64
import json
import requests
from nacl import public


def get_repo_public_key(owner, repo, token):
    url = f"https://api.github.com/repos/{owner}/{repo}/actions/secrets/public-key"
    r = requests.get(url, headers={"Authorization": f"token {token}", "Accept": "application/vnd.github+json"})
    r.raise_for_status()
    return r.json()


def encrypt_secret(public_key_b64, secret_value):
    pk = base64.b64decode(public_key_b64)
    pubkey = public.PublicKey(pk)
    sealed_box = public.SealedBox(pubkey)
    encrypted = sealed_box.encrypt(secret_value.encode('utf-8'))
    return base64.b64encode(encrypted).decode('utf-8')


def put_secret(owner, repo, token, secret_name, encrypted_value, key_id):
    url = f"https://api.github.com/repos/{owner}/{repo}/actions/secrets/{secret_name}"
    payload = {"encrypted_value": encrypted_value, "key_id": key_id}
    r = requests.put(url, headers={"Authorization": f"token {token}", "Accept": "application/vnd.github+json"}, data=json.dumps(payload))
    r.raise_for_status()
    return r.status_code


def main():
    if len(sys.argv) < 5:
        print("Usage: python gh_set_secret.py owner repo secret_name secret_value")
        sys.exit(2)

    owner = sys.argv[1]
    repo = sys.argv[2]
    secret_name = sys.argv[3]
    secret_value = sys.argv[4]

    if secret_value.startswith('@file:'):
        path = secret_value[len('@file:'):]
        with open(path, 'r', encoding='utf-8') as f:
            secret_value = f.read()

    token = os.environ.get('GITHUB_TOKEN')
    if not token:
        print('Environment variable GITHUB_TOKEN not set')
        sys.exit(2)

    key = get_repo_public_key(owner, repo, token)
    encrypted = encrypt_secret(key['key'], secret_value)
    put_secret(owner, repo, token, secret_name, encrypted, key['key_id'])
    print(f"Secret {secret_name} set for {owner}/{repo}")


if __name__ == '__main__':
    main()
