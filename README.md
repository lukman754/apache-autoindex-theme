# **Apache Autoindex Theme Installation Guide**

This guide will walk you through the installation process for the **Apache Autoindex Theme**. The theme enhances your local Apache directory index with a sleek and modern design.

## **Installation Options**

### **Option 1: Using the Install.bat Script**

1. **Download the Install.bat File:**
   - Click the link below to download the installation script:
   [Download install.bat](https://github.com/lukman754/apache-autoindex-theme/blob/main/install.bat)
   
2. **Run the Install.bat Script:**
   - After downloading, double-click the `install.bat` file to begin the installation.

---

### **Option 2: Using Git (For Git Users)**

If you prefer using Git, follow these steps:

1. **Clone the Repository:**
   Open **Command Prompt** (not PowerShell) and run the following command:

   ```bash
   git clone https://github.com/lukman754/apache-autoindex-theme
   ```

2. **Run the Installation Script:**
   Navigate to the cloned folder and run `install.bat` as mentioned in **Option 1**.

---

### **Option 3: Manual Installation via PowerShell**

Alternatively, you can use a PowerShell command to download and run the `install.bat` file automatically:

1. **Open Command Prompt (NOT PowerShell)** and enter the following command:

   ```bash
   powershell -Command "$wc = New-Object net.webclient; $wc.DownloadFile('https://raw.githubusercontent.com/Xnuvers007/apache-autoindex-theme/refs/heads/main/install.bat', 'install.bat'); Start-Process -FilePath 'install.bat' -Wait"
   ```

This command will download the `install.bat` file and execute it, completing the installation automatically.

---

## **Installation Process**

1. **Download and Run the Installation Script:**
   - The installation script will replace the existing `index.php` file in your **C:/xampp/htdocs/** directory with the new version from the theme.

2. **Installation Complete:**
   - Once the script finishes, your **Apache Autoindex** theme will be applied to your local server's directory listing.

---

### **Before Installation:**

![Before Installation](https://github.com/lukman754/localhost-dashboard/assets/43158553/ce1aa698-af21-42cd-b7d5-77ba6f0f5d19)

This is what your local directory index will look like before the theme is applied.

---

### **After Installation:**

![After Installation](https://github.com/lukman754/localhost-dashboard/assets/43158553/be525a07-bc7f-4231-841e-bd6a63300a73)

After the installation, your directory will have a modern, clean look, making your local development environment more professional and visually appealing.

---

### **Troubleshooting:**

- Ensure that you are running **Command Prompt (not PowerShell)** if you are using the PowerShell command.
- If the `install.bat` file doesn't run automatically, make sure your **Antivirus** or **Firewall** isn't blocking the script.


# Enjoy your enhanced Apache Autoindex theme! 🎉

special thank to:
1. [God](https://en.wikipedia.org/wiki/God) or [Allah SWT.](https://en.wikipedia.org/wiki/Allah)
2. [Prophet](https://en.wikipedia.org/wiki/Prophet) or [Muhammad SAW](https://en.wikipedia.org/wiki/Muhammad)
3. [Parents](https://static.vecteezy.com/system/resources/previews/020/872/296/original/illustration-of-a-mother-father-and-child-hugging-together-happy-family-concept-illustration-vector.jpg)
4. [Lukman754](https://github.com/Lukman754)
5. [Xnuvers007](https://github.com/Xnuvers007)
