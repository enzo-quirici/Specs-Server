[![Java](https://img.shields.io/badge/Java-17%2F21-blue.svg?logo=java)](https://adoptium.net/) 
[![FreeBSD](https://img.shields.io/badge/FreeBSD-supported-red.svg?logo=freebsd)](https://www.freshports.org/java/openjdk17/) 
[![GhostBSD](https://img.shields.io/badge/GhostBSD-supported-3f5cff.svg?logo=ghost)](https://www.ghostbsd.org/) 
[![Linux](https://img.shields.io/badge/Linux-supported-green.svg?logo=linux)](https://openjdk.java.net/) 
[![macOS](https://img.shields.io/badge/macOS-supported-lightgrey.svg?logo=apple)](https://adoptium.net/) 
[![Windows](https://img.shields.io/badge/Windows-supported-blue.svg?logo=windows)](https://adoptium.net/) 
[![Arch Linux](https://img.shields.io/badge/Arch-Linux-blue.svg?logo=arch-linux)](https://archlinux.org/packages/?q=openjdk) 
[![Debian](https://img.shields.io/badge/Debian-supported-a80030.svg?logo=debian)](https://packages.debian.org/search?keywords=openjdk) 
[![Fedora](https://img.shields.io/badge/Fedora-supported-294172.svg?logo=fedora)](https://src.fedoraproject.org/rpms/java-17-openjdk) 
[![Gentoo](https://img.shields.io/badge/Gentoo-supported-54487a.svg?logo=gentoo)](https://packages.gentoo.org/packages/dev-java/openjdk)

---

# Specs Server
- Specs Server allows you to make a web server that allows you to use the validation function of the [Specs](https://github.com/enzo-quirici/Specs/
  ) application.  
  ![img.png](img.png)
## How to install it :
- Go to you server folder and Clone the repository
```Bash
git clone https://github.com/enzo-quirici/Specs/tree/master  
```
- Set up a MYSQL server and add this
```MYSQL
CREATE DATABASE Specs;

USE Specs;

CREATE TABLE system_specs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    os VARCHAR(100),                       
    version VARCHAR(50),
    cpu VARCHAR(100),
    cores INT,                            
    threads INT,                    
    gpu VARCHAR(100),                      
    vram INT,
    ram INT,                    
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP        
);

```
open the file "database.php" and change the information needed for the database.

### E.G :
```Bash
// Database parameters
$host = 'localhost';     // Database host
$db = 'specs';           // Database name
$user = 'root';          // Database user
$pass = '';              // Password
$port = 3306;            // Custom MySQL port
```
## Utilisation :
- Open the Specs app and go to file --> Validate
- Enter the server address (E.G : http://127.0.0.1/specs/receiver.php)  
![img_1.png](img_1.png)
- Click on OK
- Check if it works at the address of the website (E.G : http://127.0.0.1/specs/display_data.php)  
![img_2.png](img_2.png)
