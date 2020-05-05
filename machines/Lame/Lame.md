# Lame 10.10.10.3 - Linux

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