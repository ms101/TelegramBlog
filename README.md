# TelegramBlog
Create posts on a self-hosted blog via messaging a Telegram bot

## Setup
- copy all files to webspace
  - adjust file permissions and optionally restrict direct access to updatesite.php, see **.htaccess** as an example
- set title and description in **index.php**
- create a Telegram bot and set its accesstoken in **updatesite.php**

## Usage
- post a photo with or wihtout caption or just text to your Telegram bot directly. Alternatively, add the bot to a group and post messages there (the Bot needs to be admin to read all messages).
- new messages will be fetched by default simply by accessing index.php. This behaviour might be disabled in index.php (see first variable) and e.g. triggered by a cronjob: ```php -f /path/to/updatesite.php [debug] ```

