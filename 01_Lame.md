Attempted:  1st

Date:       25012020

`nmap -A 10.10.10.3`
```
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

```
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

```

[+] Finding open SMB ports....
[+] User SMB session established on 10.10.10.3...
[+] 10.10.10.3:445 is running Unix (name:LAME) (domain:LAME)
```