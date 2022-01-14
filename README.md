# Starfiles Download Mirror
A simple Starfiles mirror to handle file downloads

## What this is
This script is designed to make the Starfiles network more robust. It's designed to store files forever, even if Starfiles goes down. This is a decentralized system and is made to last the test of time. In theory, this system is able to bypass network or nation-wide firewalls. If Starfiles were to go offline or be inaccessible for any reason, this system will keep the file online.

## How it works
When a file is uploaded to Starfiles, it is stored on Starfiles servers ready to be downloaded by the user. A mirror can query Starfiles for any file if it knows it's hash. Once the mirror obtains a copy of this file from Starfiles, it stores it in it's cache. When Starfiles goes offline, a user is able to query a mirror for a specific file. If the mirror has a copy of this file, the user is able to download it. If a mirror does not have a copy of the file, it is able to query either Starfiles, or other known mirrors for the file.

## How is this decentralized
A mirror is able to add other mirrors to it's mirrors.json file. When a user (or another mirror) tries to download the file, if the mirror does not have a copy of it, it will query the mirrors listed in mirrors.json for the file. Mirrors are able to share their mirror list. What this means is when "mirror 1" lists "mirror 2" in it's mirrors.json file, "mirror 1" will actually save "mirror 2"s mirrors.json file to call from in the future. This creates a network of mirrors that can call eachother to request files. So if Starfiles goes down, the files stay up.

## Crawl Files
There is a file called `crawl_files.php` in the base directory. You can run this file to automatically retrieve files from other mirrors, it is recommended you setup a cronjob to keep your files up to date.

## Note of Precaution
Only add trusted mirrors to your mirrors.json file. By default, only official Starfiles mirrors are in your mirrors.json. Any mirror listed in that file has the ability to send any file, without you knowing if it's the right one or not.

## Cross Compatibility
When hosting a mirror, you can only retrieve information from mirrors supporting the same format.
The minimum supported version for this build is: `1.0 Beta 1`

## Requirements
- PHP
- Curl

## Installation
1. Copy this repo to your system

Once the above is done, add your mirror to mirrors.json. This can be done like so:
```
{
    "https://mirror.domain.com/":{
        "download":"mirror.php",
        "mirrors":"mirrors.json"
    }
}
```
### What this means:
URL: https://mirror.domain.com

Download URL: https://mirror.domain.com/mirror.php

Mirror URL: https://mirror.domain.com/mirrors.json

### Recommendations
- Have multiple domains or subdomains in your mirror.json file, even if the content is identical. This allows for a more robust network and lowers downtime.
- Don't rename mirrors.json, although the doc allows custom mirror.json files... some mirrors may not be following this rule which would make your mirror inaccessible.

## Usage
Send a GET request to the download url.

Example:
https://mirror.domain.com/mirror.php?hash={filehash}

## Planned Features
- Ability to upload files
- Check SHA256 hash to confirm valid file content