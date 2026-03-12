#define MyAppName "Ordena Facil"
#define MyAppPublisher "Ordena Facil"
#define MyAppURL "https://localhost"
#ifndef MyAppVersion
  #define MyAppVersion "0.0.0-dev"
#endif
#ifndef MySourceRoot
  #define MySourceRoot "..\\..\\"
#endif
#ifndef MyOutputDir
  #define MyOutputDir "..\\..\\release"
#endif

[Setup]
AppId={{0C12126F-68AB-4C62-91AD-377FEA2F2DF2}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
AppPublisherURL={#MyAppURL}
DefaultDirName={autopf}\Ordena Facil
DefaultGroupName=Ordena Facil
OutputDir={#MyOutputDir}
OutputBaseFilename=OrdenaFacil-Installer-{#MyAppVersion}
Compression=lzma
SolidCompression=yes
WizardStyle=modern
ArchitecturesInstallIn64BitMode=x64

[Languages]
Name: "spanish"; MessagesFile: "compiler:Languages\Spanish.isl"

[Tasks]
Name: "desktopicon"; Description: "Crear acceso directo en el escritorio"; GroupDescription: "Accesos directos:"; Flags: unchecked

[Files]
Source: "{#MySourceRoot}app\*"; DestDir: "{app}\app"; Flags: recursesubdirs createallsubdirs ignoreversion
Source: "{#MySourceRoot}bootstrap\*"; DestDir: "{app}\bootstrap"; Flags: recursesubdirs createallsubdirs ignoreversion
Source: "{#MySourceRoot}config\*"; DestDir: "{app}\config"; Flags: recursesubdirs createallsubdirs ignoreversion
Source: "{#MySourceRoot}database\*"; DestDir: "{app}\database"; Flags: recursesubdirs createallsubdirs ignoreversion; Excludes: "database.sqlite"
Source: "{#MySourceRoot}public\*"; DestDir: "{app}\public"; Flags: recursesubdirs createallsubdirs ignoreversion
Source: "{#MySourceRoot}resources\*"; DestDir: "{app}\resources"; Flags: recursesubdirs createallsubdirs ignoreversion
Source: "{#MySourceRoot}routes\*"; DestDir: "{app}\routes"; Flags: recursesubdirs createallsubdirs ignoreversion
Source: "{#MySourceRoot}scripts\*"; DestDir: "{app}\scripts"; Flags: recursesubdirs createallsubdirs ignoreversion; Excludes: "runtime_processes.json"
Source: "{#MySourceRoot}storage\*"; DestDir: "{app}\storage"; Flags: recursesubdirs createallsubdirs ignoreversion; Excludes: "logs\*"
Source: "{#MySourceRoot}artisan"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}composer.json"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}composer.lock"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}.env.example"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}README.md"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}README-SETUP.md"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}README-VALIDATION.md"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}README-REPORTS.md"; DestDir: "{app}"; Flags: ignoreversion
Source: "{#MySourceRoot}INSTALLATION.md"; DestDir: "{app}"; Flags: ignoreversion

[Icons]
Name: "{group}\Ordena Facil - Iniciar"; Filename: "{sys}\WindowsPowerShell\v1.0\powershell.exe"; Parameters: "-NoProfile -ExecutionPolicy Bypass -File ""{app}\scripts\install_universal.ps1"" -ProjectDir ""{app}"""; WorkingDir: "{app}"
Name: "{group}\Ordena Facil - Actualizar"; Filename: "{sys}\WindowsPowerShell\v1.0\powershell.exe"; Parameters: "-NoProfile -ExecutionPolicy Bypass -File ""{app}\scripts\update_universal.ps1"" -ProjectDir ""{app}"""; WorkingDir: "{app}"
Name: "{group}\Ordena Facil - Desinstalar"; Filename: "{uninstallexe}"
Name: "{autodesktop}\Ordena Facil - Iniciar"; Filename: "{sys}\WindowsPowerShell\v1.0\powershell.exe"; Parameters: "-NoProfile -ExecutionPolicy Bypass -File ""{app}\scripts\install_universal.ps1"" -ProjectDir ""{app}"""; WorkingDir: "{app}"; Tasks: desktopicon

[Run]
Filename: "{sys}\WindowsPowerShell\v1.0\powershell.exe"; Parameters: "-NoProfile -ExecutionPolicy Bypass -File ""{app}\scripts\install_universal.ps1"" -ProjectDir ""{app}"""; Description: "Inicializar Ordena Facil ahora"; Flags: nowait postinstall
