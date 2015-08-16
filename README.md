# PHPCI-Jenkins-Plugin
A simple integration plugin to trigger Jenkins build from PHPCI

# Installation
Place the file under the folder <path to PHPCI>/PHPCI/plugins

# Add to project
In the PHPCI Project config section add the Jenkins trigger
complete:
    jenkins:
       url: "<url to jenkins>"
       project: "<project on which the build is called"
       token: "<if build require a login put an API Token here>"

# Set Jenkins to accept token Build
1. Go to the Project configuration
2. Open the Build Triggers section
3. Tick the option 'Trigger builds remotely (e.g., from scripts)'
4. In the Token field, create a token and make sure to set it in PHPCI configuration as above
