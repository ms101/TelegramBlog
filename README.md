# TelegramBlog
Create posts on a self-hosted blog via messaging a Telegram bot

## Setup
- copy all files to webspace
  - adjust file permissions an restrict direct access to updatesite.php, see **.htaccess** as an example
- define title and description in **index.php**
- define bot accesstoken in **updatesite.php**

## Usage
- post text or photos (or both) to your Telegram bot directly, or add the bot to a group (needs to be admin to read all messages) and post in group
- new messages will be fetched when accessing index.php by default. This behaviour be disabled in index.php by the first variable and e.g. triggered by a cronjob: ```php -f /path/to/updatesite.php [debug] ```
