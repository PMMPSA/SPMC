# PMRequisites
Essentials for PocketMine-MP

# Still in development!
This plugin is still under heavy development! At the moment, most commands are working, and in the future I look to add warps and a teleportation system.

# Features

## Commands and Permissions

- /adventure => pmrequisites.adventure
- /break => pmrequisites.break
- /broadcast => pmrequisites.broadcast
- /clearhand => pmrequisites.clearhand
- /clearhotbar => pmrequities.clearhotbar
- /clearinventory => pmrequisites.clearinventory
- /clearlag => pmrequisites.clearlag
- /compass => pmrequisites.compass
- /creative => pmrequisites.creative
- /feed => pmrequisites.feed
- /fly => pmrequisites.fly
- /getpos => pmrequisites.getpos
- /heal => pmrequisites.heal
- /kick => pmrequisites.moderation.kick
- /kickall => pmrequisites.moderation.kickall
- /nick => pmrequisites.nick
- /ping => pmrequisites.ping
- /repair => pmrequisites.repair
- /setspawn => pmrequisites.setspawn
- **/spawn => pmrequisites.spawn**
- /sudo => pmrequisites.sudo
- /supervanish => pmrequisites.supervanish
- /survival => pmrequisites.survival
- /tpa => pmrequisites.teleporation.tpa
- /tpaccept => pmrequisites.teleportation.tpaccept
- /tpahere => pmrequisites.teleportation.tpahere
- /tpdeny => pmrequisites.teleportation.tpdeny
- /vanish => pmrequisites.vanish

With SuperVanish, it is supposed to give the effect that the sender has left the server. So, I made it so the player doesn't show in the Player Listings. Sadly, PMMP had no `getLeaveMessage()` or `getJoinMessage()` function, so I had to make a configuration file for the join and leave messages. When you type `/sv` it will send the default join/leave message. You can edit this in the `config.yml` file, to say your server's leave message. I apologize for this inconvenience, but it was the best way I could get this done.

**All permissions should be working, but if you have any issues with my typos or anything, open an issue. Also, I bolded `/spawn` because when a player joins it executes the spawn command, this is temporary, but for now, I recommend setting that as a permission for default ranks**

# Installation
Since this plugin is not officially released on Poggit, you need to download it and either run it from source, or create the `.phar` for it yourself.

## GitHub Releases Page

You can download this plugin from the GitHub [releases](https://github.com/HyperFlareMC/PMRequisites/releases) page. Simply download the `.zip` folder, unzip it, then the `.phar` will be waiting for you on the inside. You can then drop it in the `plugins` directory on your server, restart it, and you are good to go!

## Poggit CI Page

You can download this plugin from the Poggit CI page at [PMRequisites](https://poggit.pmmp.io/ci/HyperFlareMC/PMRequisites/PMRequisites). Download the `.phar`, throw it into your `plugins` directory, restart it, and you're good to go!

# SUPPORT

This may seem like a complicated process, but I promise it is not. If you need additional assistance, feel free to open an Issue here on the Repository, or DM me on Discord: `HyperFlare#7018`
