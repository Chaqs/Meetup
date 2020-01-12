# Meetup
• This plugin allows you to host your own Meetup games on your PocketMine-MP Server!\
You can find the latest development phars [here](https://poggit.pmmp.io/ci/senpayeh/Meetup/Meetup)
## Before installing
Meetup is a plugin that I suggest you to use on secondary servers, but it'll work on main ones too.
## Plugin setup
### General Configuration
Before using the plugin, the first thing you want to do is go to ```plugin_data/Meetup/config.yml``` and digit the name of the world in which you want Meetups to run. You will also find other settings, such as *length* and *spectators*. If you want, you can already set your preferred values. 
Put an **Integer** number in the *length* section which is the length of the Meetup game in minutes. Example: ```length: 30```
Put a **Boolean** value in the *spectators* section. If the value is true, the players will spectate the game after dying. Example: ```spectators: true```
Put **Boolean** value in the *reset* section. This feature will remove every block placed during the Meetup game to reset the arena. Example: ```reset: false```
### Customizable kits
Once set up the ```config.yml``` file, go to ```plugin_data/Meetup/kit.yml```. In this file, you will be able to customize your kits that will be given at the start of the Meetup to the players. You will have to follow a template to use this feature correctly:
```item: "itemID:metadataID:count:enchantName:enchantLevel"``` - you will found a default kit in the file to help you making your own kit.
**Note:** if you don't want an armor piece, the whole value will be ```0:0:0:0:0``` otherwise the kit won't work. If you don't want any enchantment, put ```0:0``` on the ```enchantName``` and ```enchantLevel``` parts.
### Customizable messages
If you want to use custom messages for your Meetup game, you can simply go to ```plugin_data/Meetup/messages.yml``` file and edit all the texts. Use **%player%** to indicate the winner of the Meetup. There are examples to help you make your own messages.
### Scenarios
• Enabling NoClean, players will get a regeneration effect for 10 seconds after killing a player to prevent cleanupping (1.1 update)
## Issues
If you are encountering issues, make sure to add one in the relative tab.
## Acknowledgments
Thanks for downloading the plugin. If you want to support me, drop a star!
Made with ♡ by Senpayeh
