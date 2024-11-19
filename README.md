# Specs Server
- Specs Server allows you to make a web server that allows you to use the "validation" function of the [Specs](https://github.com/enzo-quirici/Specs/blob/master/INSTALL.md
  ) application.  
  ![img.png](img.png)
## How to use :
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