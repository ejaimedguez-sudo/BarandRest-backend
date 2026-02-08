#!/bin/bash
set -e
# Append deploy public key to authorized_keys
sudo mkdir -p /root/.ssh
if ! sudo grep -q -f /root/creds/deploy_key_new.pub /root/.ssh/authorized_keys 2>/dev/null; then
  sudo bash -c 'cat /root/creds/deploy_key_new.pub >> /root/.ssh/authorized_keys'
  sudo chmod 600 /root/.ssh/authorized_keys
  sudo chown root:root /root/.ssh/authorized_keys
  echo "Appended deploy public key"
else
  echo "Public key already present"
fi
# Ensure python3 nacl package exists
if ! python3 -c "import nacl" 2>/dev/null; then
  echo "python3-nacl not found, installing python3-pip and pynacl..."
  sudo apt-get update -y
  sudo apt-get install -y python3-pip
  sudo pip3 install pynacl
fi
# Run secrets upload script
sudo bash /mnt/c/xampp/htdocs/apps/BarandRest/set_github_secrets.sh
