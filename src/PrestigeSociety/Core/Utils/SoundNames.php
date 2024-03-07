<?php

namespace PrestigeSociety\Core\Utils;
interface SoundNames
{
    public const SOUND_AMBIENT_WEATHER_THUNDER = "ambient.weather.thunder";
    public const SOUND_AMBIENT_WEATHER_LIGHTNING_IMPACT = "ambient.weather.lightning.impact";
    public const SOUND_AMBIENT_WEATHER_RAIN = "ambient.weather.rain";
    public const SOUND_BEACON_ACTIVATE = "beacon.activate";
    public const SOUND_BEACON_AMBIENT = "beacon.ambient";
    public const SOUND_BEACON_DEACTIVATE = "beacon.deactivate";
    public const SOUND_BEACON_POWER = "beacon.power";
    public const SOUND_BLOCK_LANTERN_BREAK = "block.lantern.break";
    public const SOUND_BLOCK_LANTERN_FALL = "block.lantern.fall";
    public const SOUND_BLOCK_LANTERN_HIT = "block.lantern.hit";
    public const SOUND_BLOCK_LANTERN_PLACE = "block.lantern.place";
    public const SOUND_BLOCK_LANTERN_STEP = "block.lantern.step";
    public const SOUND_BLOCK_BAMBOO_BREAK = "block.bamboo.break";
    public const SOUND_BLOCK_BAMBOO_FALL = "block.bamboo.fall";
    public const SOUND_BLOCK_BAMBOO_HIT = "block.bamboo.hit";
    public const SOUND_BLOCK_BAMBOO_PLACE = "block.bamboo.place";
    public const SOUND_BLOCK_BAMBOO_STEP = "block.bamboo.step";
    public const SOUND_BLOCK_BAMBOO_SAPLING_PLACE = "block.bamboo_sapling.place";
    public const SOUND_BLOCK_BAMBOO_SAPLING_BREAK = "block.bamboo_sapling.break";
    public const SOUND_BLOCK_CAMPFIRE_CRACKLE = "block.campfire.crackle";
    public const SOUND_BLOCK_BARREL_CLOSE = "block.barrel.close";
    public const SOUND_BLOCK_BARREL_OPEN = "block.barrel.open";
    public const SOUND_BLOCK_FALSE_PERMISSIONS = "block.false_permissions";
    public const SOUND_BLOCK_END_PORTAL_SPAWN = "block.end_portal.spawn";
    public const SOUND_BLOCK_END_PORTAL_FRAME_FILL = "block.end_portal_frame.fill";
    public const SOUND_BLOCK_ITEMFRAME_ADD_ITEM = "block.itemframe.add_item";
    public const SOUND_BLOCK_ITEMFRAME_BREAK = "block.itemframe.break";
    public const SOUND_BLOCK_ITEMFRAME_PLACE = "block.itemframe.place";
    public const SOUND_BLOCK_ITEMFRAME_REMOVE_ITEM = "block.itemframe.remove_item";
    public const SOUND_BLOCK_ITEMFRAME_ROTATE_ITEM = "block.itemframe.rotate_item";
    public const SOUND_BLOCK_CHORUSFLOWER_DEATH = "block.chorusflower.death";
    public const SOUND_BLOCK_CHORUSFLOWER_GROW = "block.chorusflower.grow";
    public const SOUND_BLOCK_COMPOSTER_FILL = "block.composter.fill";
    public const SOUND_BLOCK_COMPOSTER_FILL_SUCCESS = "block.composter.fill_success";
    public const SOUND_BLOCK_COMPOSTER_EMPTY = "block.composter.empty";
    public const SOUND_BLOCK_COMPOSTER_READY = "block.composter.ready";
    public const SOUND_BLOCK_LOOM_USE = "block.loom.use";
    public const SOUND_UI_LOOM_TAKE_RESULT = "ui.loom.take_result";
    public const SOUND_UI_CARTOGRAPHY_TABLE_TAKE_RESULT = "ui.cartography_table.take_result";
    public const SOUND_UI_LOOM_SELECT_PATTERN = "ui.loom.select_pattern";
    public const SOUND_BLOCK_STONECUTTER_USE = "block.stonecutter.use";
    public const SOUND_UI_STONECUTTER_TAKE_RESULT = "ui.stonecutter.take_result";
    public const SOUND_BLOCK_CARTOGRAPHY_TABLE_USE = "block.cartography_table.use";
    public const SOUND_BLOCK_GRINDSTONE_USE = "block.grindstone.use";
    public const SOUND_BLOCK_TURTLE_EGG_DROP = "block.turtle_egg.drop";
    public const SOUND_BLOCK_TURTLE_EGG_BREAK = "block.turtle_egg.break";
    public const SOUND_BLOCK_TURTLE_EGG_CRACK = "block.turtle_egg.crack";
    public const SOUND_BLOCK_SCAFFOLDING_BREAK = "block.scaffolding.break";
    public const SOUND_BLOCK_SCAFFOLDING_FALL = "block.scaffolding.fall";
    public const SOUND_BLOCK_SCAFFOLDING_HIT = "block.scaffolding.hit";
    public const SOUND_BLOCK_SCAFFOLDING_PLACE = "block.scaffolding.place";
    public const SOUND_BLOCK_SCAFFOLDING_STEP = "block.scaffolding.step";
    public const SOUND_BLOCK_SCAFFOLDING_CLIMB = "block.scaffolding.climb";
    public const SOUND_BLOCK_SWEET_BERRY_BUSH_BREAK = "block.sweet_berry_bush.break";
    public const SOUND_BLOCK_SWEET_BERRY_BUSH_PLACE = "block.sweet_berry_bush.place";
    public const SOUND_BLOCK_SWEET_BERRY_BUSH_HURT = "block.sweet_berry_bush.hurt";
    public const SOUND_BLOCK_SWEET_BERRY_BUSH_PICK = "block.sweet_berry_bush.pick";
    public const SOUND_BUCKET_EMPTY_LAVA = "bucket.empty_lava";
    public const SOUND_BUCKET_EMPTY_WATER = "bucket.empty_water";
    public const SOUND_BUCKET_FILL_LAVA = "bucket.fill_lava";
    public const SOUND_BUCKET_FILL_WATER = "bucket.fill_water";
    public const SOUND_BUCKET_FILL_FISH = "bucket.fill_fish";
    public const SOUND_BUCKET_EMPTY_FISH = "bucket.empty_fish";
    public const SOUND_BOTTLE_DRAGONBREATH = "bottle.dragonbreath";
    public const SOUND_CAULDRON_EXPLODE = "cauldron.explode";
    public const SOUND_CAULDRON_DYEARMOR = "cauldron.dyearmor";
    public const SOUND_CAULDRON_CLEANARMOR = "cauldron.cleanarmor";
    public const SOUND_CAULDRON_CLEANBANNER = "cauldron.cleanbanner";
    public const SOUND_CAULDRON_FILLPOTION = "cauldron.fillpotion";
    public const SOUND_CAULDRON_TAKEPOTION = "cauldron.takepotion";
    public const SOUND_CAULDRON_FILLWATER = "cauldron.fillwater";
    public const SOUND_CAULDRON_TAKEWATER = "cauldron.takewater";
    public const SOUND_CAULDRON_ADDDYE = "cauldron.adddye";
    public const SOUND_CONDUIT_ACTIVATE = "conduit.activate";
    public const SOUND_CONDUIT_AMBIENT = "conduit.ambient";
    public const SOUND_CONDUIT_ATTACK = "conduit.attack";
    public const SOUND_CONDUIT_DEACTIVATE = "conduit.deactivate";
    public const SOUND_CONDUIT_SHORT = "conduit.short";
    public const SOUND_CROSSBOW_LOADING_START = "crossbow.loading.start";
    public const SOUND_CROSSBOW_LOADING_MIDDLE = "crossbow.loading.middle";
    public const SOUND_CROSSBOW_LOADING_END = "crossbow.loading.end";
    public const SOUND_CROSSBOW_SHOOT = "crossbow.shoot";
    public const SOUND_CROSSBOW_QUICK_CHARGE_START = "crossbow.quick_charge.start";
    public const SOUND_CROSSBOW_QUICK_CHARGE_MIDDLE = "crossbow.quick_charge.middle";
    public const SOUND_CROSSBOW_QUICK_CHARGE_END = "crossbow.quick_charge.end";
    public const SOUND_DAMAGE_FALLBIG = "damage.fallbig";
    public const SOUND_DAMAGE_FALLSMALL = "damage.fallsmall";
    public const SOUND_ELYTRA_LOOP = "elytra.loop";
    public const SOUND_GAME_PLAYER_ATTACK_NODAMAGE = "game.player.attack.nodamage";
    public const SOUND_GAME_PLAYER_ATTACK_STRONG = "game.player.attack.strong";
    public const SOUND_GAME_PLAYER_HURT = "game.player.hurt";
    public const SOUND_GAME_PLAYER_DIE = "game.player.die";
    public const SOUND_DIG_CLOTH = "dig.cloth";
    public const SOUND_DIG_GRASS = "dig.grass";
    public const SOUND_DIG_GRAVEL = "dig.gravel";
    public const SOUND_DIG_SAND = "dig.sand";
    public const SOUND_DIG_SNOW = "dig.snow";
    public const SOUND_DIG_STONE = "dig.stone";
    public const SOUND_DIG_WOOD = "dig.wood";
    public const SOUND_TILE_PISTON_IN = "tile.piston.in";
    public const SOUND_TILE_PISTON_OUT = "tile.piston.out";
    public const SOUND_FIRE_FIRE = "fire.fire";
    public const SOUND_FIRE_IGNITE = "fire.ignite";
    public const SOUND_LEASHKNOT_BREAK = "leashknot.break";
    public const SOUND_LEASHKNOT_PLACE = "leashknot.place";
    public const SOUND_FIREWORK_BLAST = "firework.blast";
    public const SOUND_FIREWORK_LARGE_BLAST = "firework.large_blast";
    public const SOUND_FIREWORK_LAUNCH = "firework.launch";
    public const SOUND_FIREWORK_SHOOT = "firework.shoot";
    public const SOUND_FIREWORK_TWINKLE = "firework.twinkle";
    public const SOUND_ARMOR_EQUIP_CHAIN = "armor.equip_chain";
    public const SOUND_ARMOR_EQUIP_DIAMOND = "armor.equip_diamond";
    public const SOUND_ARMOR_EQUIP_GENERIC = "armor.equip_generic";
    public const SOUND_ARMOR_EQUIP_GOLD = "armor.equip_gold";
    public const SOUND_ARMOR_EQUIP_IRON = "armor.equip_iron";
    public const SOUND_ARMOR_EQUIP_LEATHER = "armor.equip_leather";
    public const SOUND_LIQUID_LAVA = "liquid.lava";
    public const SOUND_LIQUID_LAVAPOP = "liquid.lavapop";
    public const SOUND_LIQUID_WATER = "liquid.water";
    public const SOUND_BUBBLE_POP = "bubble.pop";
    public const SOUND_BUBBLE_UP = "bubble.up";
    public const SOUND_BUBBLE_UPINSIDE = "bubble.upinside";
    public const SOUND_BUBBLE_DOWN = "bubble.down";
    public const SOUND_BUBBLE_DOWNINSIDE = "bubble.downinside";
    public const SOUND_MINECART_BASE = "minecart.base";
    public const SOUND_MINECART_INSIDE = "minecart.inside";
    public const SOUND_BLOCK_FURNACE_LIT = "block.furnace.lit";
    public const SOUND_BLOCK_BLASTFURNACE_FIRE_CRACKLE = "block.blastfurnace.fire_crackle";
    public const SOUND_BLOCK_SMOKER_SMOKE = "block.smoker.smoke";
    public const SOUND_MOB_AGENT_SPAWN = "mob.agent.spawn";
    public const SOUND_MOB_ARMOR_STAND_BREAK = "mob.armor_stand.break";
    public const SOUND_MOB_ARMOR_STAND_HIT = "mob.armor_stand.hit";
    public const SOUND_MOB_ARMOR_STAND_LAND = "mob.armor_stand.land";
    public const SOUND_MOB_ARMOR_STAND_PLACE = "mob.armor_stand.place";
    public const SOUND_MOB_BAT_DEATH = "mob.bat.death";
    public const SOUND_MOB_BAT_HURT = "mob.bat.hurt";
    public const SOUND_MOB_BAT_IDLE = "mob.bat.idle";
    public const SOUND_MOB_BAT_TAKEOFF = "mob.bat.takeoff";
    public const SOUND_MOB_BLAZE_BREATHE = "mob.blaze.breathe";
    public const SOUND_MOB_BLAZE_DEATH = "mob.blaze.death";
    public const SOUND_MOB_BLAZE_HIT = "mob.blaze.hit";
    public const SOUND_MOB_BLAZE_SHOOT = "mob.blaze.shoot";
    public const SOUND_MOB_CHICKEN_HURT = "mob.chicken.hurt";
    public const SOUND_MOB_CHICKEN_PLOP = "mob.chicken.plop";
    public const SOUND_MOB_CHICKEN_SAY = "mob.chicken.say";
    public const SOUND_MOB_CHICKEN_STEP = "mob.chicken.step";
    public const SOUND_MOB_COW_HURT = "mob.cow.hurt";
    public const SOUND_MOB_COW_SAY = "mob.cow.say";
    public const SOUND_MOB_COW_STEP = "mob.cow.step";
    public const SOUND_MOB_COW_MILK = "mob.cow.milk";
    public const SOUND_MOB_CREEPER_DEATH = "mob.creeper.death";
    public const SOUND_MOB_CREEPER_SAY = "mob.creeper.say";
    public const SOUND_MOB_DOLPHIN_IDLE_WATER = "mob.dolphin.idle_water";
    public const SOUND_MOB_DOLPHIN_ATTACK = "mob.dolphin.attack";
    public const SOUND_MOB_DOLPHIN_BLOWHOLE = "mob.dolphin.blowhole";
    public const SOUND_MOB_DOLPHIN_DEATH = "mob.dolphin.death";
    public const SOUND_MOB_DOLPHIN_EAT = "mob.dolphin.eat";
    public const SOUND_MOB_DOLPHIN_HURT = "mob.dolphin.hurt";
    public const SOUND_MOB_DOLPHIN_IDLE = "mob.dolphin.idle";
    public const SOUND_MOB_DOLPHIN_JUMP = "mob.dolphin.jump";
    public const SOUND_MOB_DOLPHIN_PLAY = "mob.dolphin.play";
    public const SOUND_MOB_DOLPHIN_SPLASH = "mob.dolphin.splash";
    public const SOUND_MOB_DOLPHIN_SWIM = "mob.dolphin.swim";
    public const SOUND_MOB_DROWNED_SAY_WATER = "mob.drowned.say_water";
    public const SOUND_MOB_DROWNED_DEATH_WATER = "mob.drowned.death_water";
    public const SOUND_MOB_DROWNED_HURT_WATER = "mob.drowned.hurt_water";
    public const SOUND_MOB_DROWNED_SAY = "mob.drowned.say";
    public const SOUND_MOB_DROWNED_DEATH = "mob.drowned.death";
    public const SOUND_MOB_DROWNED_HURT = "mob.drowned.hurt";
    public const SOUND_MOB_DROWNED_SHOOT = "mob.drowned.shoot";
    public const SOUND_MOB_DROWNED_STEP = "mob.drowned.step";
    public const SOUND_MOB_DROWNED_SWIM = "mob.drowned.swim";
    public const SOUND_ENTITY_ZOMBIE_CONVERTED_TO_DROWNED = "entity.zombie.converted_to_drowned";
    public const SOUND_MOB_ENDERMEN_DEATH = "mob.endermen.death";
    public const SOUND_MOB_ENDERMEN_HIT = "mob.endermen.hit";
    public const SOUND_MOB_ENDERMEN_IDLE = "mob.endermen.idle";
    public const SOUND_MOB_ENDERMEN_PORTAL = "mob.endermen.portal";
    public const SOUND_MOB_ENDERMEN_SCREAM = "mob.endermen.scream";
    public const SOUND_MOB_ENDERMEN_STARE = "mob.endermen.stare";
    public const SOUND_MOB_ENDERDRAGON_DEATH = "mob.enderdragon.death";
    public const SOUND_MOB_ENDERDRAGON_HIT = "mob.enderdragon.hit";
    public const SOUND_MOB_ENDERDRAGON_FLAP = "mob.enderdragon.flap";
    public const SOUND_MOB_ENDERDRAGON_GROWL = "mob.enderdragon.growl";
    public const SOUND_MOB_FOX_AMBIENT = "mob.fox.ambient";
    public const SOUND_MOB_FOX_HURT = "mob.fox.hurt";
    public const SOUND_MOB_FOX_DEATH = "mob.fox.death";
    public const SOUND_MOB_FOX_AGGRO = "mob.fox.aggro";
    public const SOUND_MOB_FOX_SNIFF = "mob.fox.sniff";
    public const SOUND_MOB_FOX_BITE = "mob.fox.bite";
    public const SOUND_MOB_FOX_EAT = "mob.fox.eat";
    public const SOUND_MOB_FOX_SCREECH = "mob.fox.screech";
    public const SOUND_MOB_FOX_SLEEP = "mob.fox.sleep";
    public const SOUND_MOB_FOX_SPIT = "mob.fox.spit";
    public const SOUND_MOB_GHAST_AFFECTIONATE_SCREAM = "mob.ghast.affectionate_scream";
    public const SOUND_MOB_GHAST_CHARGE = "mob.ghast.charge";
    public const SOUND_MOB_GHAST_DEATH = "mob.ghast.death";
    public const SOUND_MOB_GHAST_FIREBALL = "mob.ghast.fireball";
    public const SOUND_MOB_GHAST_MOAN = "mob.ghast.moan";
    public const SOUND_MOB_GHAST_SCREAM = "mob.ghast.scream";
    public const SOUND_MOB_GUARDIAN_AMBIENT = "mob.guardian.ambient";
    public const SOUND_MOB_GUARDIAN_ATTACK_LOOP = "mob.guardian.attack_loop";
    public const SOUND_MOB_ELDERGUARDIAN_CURSE = "mob.elderguardian.curse";
    public const SOUND_MOB_ELDERGUARDIAN_DEATH = "mob.elderguardian.death";
    public const SOUND_MOB_ELDERGUARDIAN_HIT = "mob.elderguardian.hit";
    public const SOUND_MOB_ELDERGUARDIAN_IDLE = "mob.elderguardian.idle";
    public const SOUND_MOB_GUARDIAN_FLOP = "mob.guardian.flop";
    public const SOUND_MOB_GUARDIAN_DEATH = "mob.guardian.death";
    public const SOUND_MOB_GUARDIAN_HIT = "mob.guardian.hit";
    public const SOUND_MOB_GUARDIAN_LAND_DEATH = "mob.guardian.land_death";
    public const SOUND_MOB_GUARDIAN_LAND_HIT = "mob.guardian.land_hit";
    public const SOUND_MOB_GUARDIAN_LAND_IDLE = "mob.guardian.land_idle";
    public const SOUND_MOB_FISH_FLOP = "mob.fish.flop";
    public const SOUND_MOB_FISH_HURT = "mob.fish.hurt";
    public const SOUND_MOB_FISH_STEP = "mob.fish.step";
    public const SOUND_MOB_LLAMA_ANGRY = "mob.llama.angry";
    public const SOUND_MOB_LLAMA_DEATH = "mob.llama.death";
    public const SOUND_MOB_LLAMA_IDLE = "mob.llama.idle";
    public const SOUND_MOB_LLAMA_SPIT = "mob.llama.spit";
    public const SOUND_MOB_LLAMA_HURT = "mob.llama.hurt";
    public const SOUND_MOB_LLAMA_EAT = "mob.llama.eat";
    public const SOUND_MOB_LLAMA_STEP = "mob.llama.step";
    public const SOUND_MOB_LLAMA_SWAG = "mob.llama.swag";
    public const SOUND_MOB_HORSE_ANGRY = "mob.horse.angry";
    public const SOUND_MOB_HORSE_ARMOR = "mob.horse.armor";
    public const SOUND_MOB_HORSE_BREATHE = "mob.horse.breathe";
    public const SOUND_MOB_HORSE_DEATH = "mob.horse.death";
    public const SOUND_MOB_HORSE_DONKEY_ANGRY = "mob.horse.donkey.angry";
    public const SOUND_MOB_HORSE_DONKEY_DEATH = "mob.horse.donkey.death";
    public const SOUND_MOB_HORSE_DONKEY_HIT = "mob.horse.donkey.hit";
    public const SOUND_MOB_HORSE_DONKEY_IDLE = "mob.horse.donkey.idle";
    public const SOUND_MOB_HORSE_EAT = "mob.horse.eat";
    public const SOUND_MOB_HORSE_GALLOP = "mob.horse.gallop";
    public const SOUND_MOB_HORSE_HIT = "mob.horse.hit";
    public const SOUND_MOB_HORSE_IDLE = "mob.horse.idle";
    public const SOUND_MOB_HORSE_JUMP = "mob.horse.jump";
    public const SOUND_MOB_HORSE_LAND = "mob.horse.land";
    public const SOUND_MOB_HORSE_LEATHER = "mob.horse.leather";
    public const SOUND_MOB_HORSE_SKELETON_DEATH = "mob.horse.skeleton.death";
    public const SOUND_MOB_HORSE_SKELETON_HIT = "mob.horse.skeleton.hit";
    public const SOUND_MOB_HORSE_SKELETON_IDLE = "mob.horse.skeleton.idle";
    public const SOUND_MOB_HORSE_SOFT = "mob.horse.soft";
    public const SOUND_MOB_HORSE_WOOD = "mob.horse.wood";
    public const SOUND_MOB_HORSE_ZOMBIE_DEATH = "mob.horse.zombie.death";
    public const SOUND_MOB_HORSE_ZOMBIE_HIT = "mob.horse.zombie.hit";
    public const SOUND_MOB_HORSE_ZOMBIE_IDLE = "mob.horse.zombie.idle";
    public const SOUND_MOB_HUSK_AMBIENT = "mob.husk.ambient";
    public const SOUND_MOB_HUSK_DEATH = "mob.husk.death";
    public const SOUND_MOB_HUSK_HURT = "mob.husk.hurt";
    public const SOUND_MOB_HUSK_STEP = "mob.husk.step";
    public const SOUND_MOB_RAVAGER_AMBIENT = "mob.ravager.ambient";
    public const SOUND_MOB_RAVAGER_BITE = "mob.ravager.bite";
    public const SOUND_MOB_RAVAGER_CELEBRATE = "mob.ravager.celebrate";
    public const SOUND_MOB_RAVAGER_DEATH = "mob.ravager.death";
    public const SOUND_MOB_RAVAGER_HURT = "mob.ravager.hurt";
    public const SOUND_MOB_RAVAGER_ROAR = "mob.ravager.roar";
    public const SOUND_MOB_RAVAGER_STEP = "mob.ravager.step";
    public const SOUND_MOB_RAVAGER_STUN = "mob.ravager.stun";
    public const SOUND_MOB_IRONGOLEM_THROW = "mob.irongolem.throw";
    public const SOUND_MOB_IRONGOLEM_DEATH = "mob.irongolem.death";
    public const SOUND_MOB_IRONGOLEM_HIT = "mob.irongolem.hit";
    public const SOUND_MOB_IRONGOLEM_WALK = "mob.irongolem.walk";
    public const SOUND_MOB_SHULKER_AMBIENT = "mob.shulker.ambient";
    public const SOUND_MOB_SHULKER_CLOSE = "mob.shulker.close";
    public const SOUND_MOB_SHULKER_DEATH = "mob.shulker.death";
    public const SOUND_MOB_SHULKER_CLOSE_HURT = "mob.shulker.close.hurt";
    public const SOUND_MOB_SHULKER_HURT = "mob.shulker.hurt";
    public const SOUND_MOB_SHULKER_OPEN = "mob.shulker.open";
    public const SOUND_MOB_SHULKER_SHOOT = "mob.shulker.shoot";
    public const SOUND_MOB_SHULKER_TELEPORT = "mob.shulker.teleport";
    public const SOUND_MOB_SHULKER_BULLET_HIT = "mob.shulker.bullet.hit";
    public const SOUND_MOB_MAGMACUBE_BIG = "mob.magmacube.big";
    public const SOUND_MOB_MAGMACUBE_JUMP = "mob.magmacube.jump";
    public const SOUND_MOB_MAGMACUBE_SMALL = "mob.magmacube.small";
    public const SOUND_MOB_MOOSHROOM_CONVERT = "mob.mooshroom.convert";
    public const SOUND_MOB_MOOSHROOM_EAT = "mob.mooshroom.eat";
    public const SOUND_MOB_MOOSHROOM_SUSPICIOUS_MILK = "mob.mooshroom.suspicious_milk";
    public const SOUND_MOB_PARROT_IDLE = "mob.parrot.idle";
    public const SOUND_MOB_PARROT_HURT = "mob.parrot.hurt";
    public const SOUND_MOB_PARROT_DEATH = "mob.parrot.death";
    public const SOUND_MOB_PARROT_STEP = "mob.parrot.step";
    public const SOUND_MOB_PARROT_EAT = "mob.parrot.eat";
    public const SOUND_MOB_PARROT_FLY = "mob.parrot.fly";
    public const SOUND_MOB_PHANTOM_BITE = "mob.phantom.bite";
    public const SOUND_MOB_PHANTOM_DEATH = "mob.phantom.death";
    public const SOUND_MOB_PHANTOM_HURT = "mob.phantom.hurt";
    public const SOUND_MOB_PHANTOM_IDLE = "mob.phantom.idle";
    public const SOUND_MOB_PHANTOM_SWOOP = "mob.phantom.swoop";
    public const SOUND_MOB_PIG_DEATH = "mob.pig.death";
    public const SOUND_MOB_PIG_BOOST = "mob.pig.boost";
    public const SOUND_MOB_PIG_SAY = "mob.pig.say";
    public const SOUND_MOB_PIG_STEP = "mob.pig.step";
    public const SOUND_MOB_PILLAGER_CELEBRATE = "mob.pillager.celebrate";
    public const SOUND_MOB_PILLAGER_DEATH = "mob.pillager.death";
    public const SOUND_MOB_PILLAGER_HURT = "mob.pillager.hurt";
    public const SOUND_MOB_PILLAGER_IDLE = "mob.pillager.idle";
    public const SOUND_MOB_RABBIT_HURT = "mob.rabbit.hurt";
    public const SOUND_MOB_RABBIT_IDLE = "mob.rabbit.idle";
    public const SOUND_MOB_RABBIT_HOP = "mob.rabbit.hop";
    public const SOUND_MOB_RABBIT_DEATH = "mob.rabbit.death";
    public const SOUND_MOB_SHEEP_SAY = "mob.sheep.say";
    public const SOUND_MOB_SHEEP_SHEAR = "mob.sheep.shear";
    public const SOUND_MOB_SHEEP_STEP = "mob.sheep.step";
    public const SOUND_MOB_SILVERFISH_HIT = "mob.silverfish.hit";
    public const SOUND_MOB_SILVERFISH_KILL = "mob.silverfish.kill";
    public const SOUND_MOB_SILVERFISH_SAY = "mob.silverfish.say";
    public const SOUND_MOB_SILVERFISH_STEP = "mob.silverfish.step";
    public const SOUND_MOB_ENDERMITE_HIT = "mob.endermite.hit";
    public const SOUND_MOB_ENDERMITE_KILL = "mob.endermite.kill";
    public const SOUND_MOB_ENDERMITE_SAY = "mob.endermite.say";
    public const SOUND_MOB_ENDERMITE_STEP = "mob.endermite.step";
    public const SOUND_MOB_SKELETON_DEATH = "mob.skeleton.death";
    public const SOUND_MOB_SKELETON_HURT = "mob.skeleton.hurt";
    public const SOUND_MOB_SKELETON_SAY = "mob.skeleton.say";
    public const SOUND_MOB_SKELETON_STEP = "mob.skeleton.step";
    public const SOUND_MOB_SLIME_BIG = "mob.slime.big";
    public const SOUND_MOB_SLIME_SMALL = "mob.slime.small";
    public const SOUND_MOB_SLIME_ATTACK = "mob.slime.attack";
    public const SOUND_MOB_SLIME_DEATH = "mob.slime.death";
    public const SOUND_MOB_SLIME_HURT = "mob.slime.hurt";
    public const SOUND_MOB_SLIME_JUMP = "mob.slime.jump";
    public const SOUND_MOB_SLIME_SQUISH = "mob.slime.squish";
    public const SOUND_MOB_SNOWGOLEM_DEATH = "mob.snowgolem.death";
    public const SOUND_MOB_SNOWGOLEM_HURT = "mob.snowgolem.hurt";
    public const SOUND_MOB_SNOWGOLEM_SHOOT = "mob.snowgolem.shoot";
    public const SOUND_MOB_SPIDER_DEATH = "mob.spider.death";
    public const SOUND_MOB_SPIDER_SAY = "mob.spider.say";
    public const SOUND_MOB_SPIDER_STEP = "mob.spider.step";
    public const SOUND_MOB_SQUID_AMBIENT = "mob.squid.ambient";
    public const SOUND_MOB_SQUID_DEATH = "mob.squid.death";
    public const SOUND_MOB_SQUID_HURT = "mob.squid.hurt";
    public const SOUND_MOB_TURTLE_AMBIENT = "mob.turtle.ambient";
    public const SOUND_MOB_TURTLE_BABY_BORN = "mob.turtle_baby.born";
    public const SOUND_MOB_TURTLE_DEATH = "mob.turtle.death";
    public const SOUND_MOB_TURTLE_BABY_DEATH = "mob.turtle_baby.death";
    public const SOUND_MOB_TURTLE_HURT = "mob.turtle.hurt";
    public const SOUND_MOB_TURTLE_BABY_HURT = "mob.turtle_baby.hurt";
    public const SOUND_MOB_TURTLE_STEP = "mob.turtle.step";
    public const SOUND_MOB_TURTLE_BABY_STEP = "mob.turtle_baby.step";
    public const SOUND_MOB_TURTLE_SWIM = "mob.turtle.swim";
    public const SOUND_MOB_STRAY_AMBIENT = "mob.stray.ambient";
    public const SOUND_MOB_STRAY_DEATH = "mob.stray.death";
    public const SOUND_MOB_STRAY_HURT = "mob.stray.hurt";
    public const SOUND_MOB_STRAY_STEP = "mob.stray.step";
    public const SOUND_MOB_VILLAGER_DEATH = "mob.villager.death";
    public const SOUND_MOB_VILLAGER_HAGGLE = "mob.villager.haggle";
    public const SOUND_MOB_VILLAGER_HIT = "mob.villager.hit";
    public const SOUND_MOB_VILLAGER_IDLE = "mob.villager.idle";
    public const SOUND_MOB_VILLAGER_NO = "mob.villager.no";
    public const SOUND_MOB_VILLAGER_YES = "mob.villager.yes";
    public const SOUND_MOB_VINDICATOR_CELEBRATE = "mob.vindicator.celebrate";
    public const SOUND_MOB_VINDICATOR_DEATH = "mob.vindicator.death";
    public const SOUND_MOB_VINDICATOR_HURT = "mob.vindicator.hurt";
    public const SOUND_MOB_VINDICATOR_IDLE = "mob.vindicator.idle";
    public const SOUND_MOB_EVOCATION_FANGS_ATTACK = "mob.evocation_fangs.attack";
    public const SOUND_MOB_EVOCATION_ILLAGER_AMBIENT = "mob.evocation_illager.ambient";
    public const SOUND_MOB_EVOCATION_ILLAGER_CAST_SPELL = "mob.evocation_illager.cast_spell";
    public const SOUND_MOB_EVOCATION_ILLAGER_CELEBRATE = "mob.evocation_illager.celebrate";
    public const SOUND_MOB_EVOCATION_ILLAGER_DEATH = "mob.evocation_illager.death";
    public const SOUND_MOB_EVOCATION_ILLAGER_HURT = "mob.evocation_illager.hurt";
    public const SOUND_MOB_EVOCATION_ILLAGER_PREPARE_ATTACK = "mob.evocation_illager.prepare_attack";
    public const SOUND_MOB_EVOCATION_ILLAGER_PREPARE_SUMMON = "mob.evocation_illager.prepare_summon";
    public const SOUND_MOB_EVOCATION_ILLAGER_PREPARE_WOLOLO = "mob.evocation_illager.prepare_wololo";
    public const SOUND_MOB_VEX_AMBIENT = "mob.vex.ambient";
    public const SOUND_MOB_VEX_DEATH = "mob.vex.death";
    public const SOUND_MOB_VEX_HURT = "mob.vex.hurt";
    public const SOUND_MOB_VEX_CHARGE = "mob.vex.charge";
    public const SOUND_ITEM_BOOK_PAGE_TURN = "item.book.page_turn";
    public const SOUND_ITEM_BOOK_PUT = "item.book.put";
    public const SOUND_BLOCK_BELL_HIT = "block.bell.hit";
    public const SOUND_ITEM_TRIDENT_HIT_GROUND = "item.trident.hit_ground";
    public const SOUND_ITEM_TRIDENT_HIT = "item.trident.hit";
    public const SOUND_ITEM_TRIDENT_RETURN = "item.trident.return";
    public const SOUND_ITEM_TRIDENT_RIPTIDE_1 = "item.trident.riptide_1";
    public const SOUND_ITEM_TRIDENT_RIPTIDE_2 = "item.trident.riptide_2";
    public const SOUND_ITEM_TRIDENT_RIPTIDE_3 = "item.trident.riptide_3";
    public const SOUND_ITEM_TRIDENT_THROW = "item.trident.throw";
    public const SOUND_ITEM_TRIDENT_THUNDER = "item.trident.thunder";
    public const SOUND_ITEM_SHIELD_BLOCK = "item.shield.block";
    public const SOUND_MOB_WANDERINGTRADER_IDLE = "mob.wanderingtrader.idle";
    public const SOUND_MOB_WANDERINGTRADER_DEATH = "mob.wanderingtrader.death";
    public const SOUND_MOB_WANDERINGTRADER_DISAPPEARED = "mob.wanderingtrader.disappeared";
    public const SOUND_MOB_WANDERINGTRADER_DRINK_MILK = "mob.wanderingtrader.drink_milk";
    public const SOUND_MOB_WANDERINGTRADER_DRINK_POTION = "mob.wanderingtrader.drink_potion";
    public const SOUND_MOB_WANDERINGTRADER_HAGGLE = "mob.wanderingtrader.haggle";
    public const SOUND_MOB_WANDERINGTRADER_YES = "mob.wanderingtrader.yes";
    public const SOUND_MOB_WANDERINGTRADER_NO = "mob.wanderingtrader.no";
    public const SOUND_MOB_WANDERINGTRADER_HURT = "mob.wanderingtrader.hurt";
    public const SOUND_MOB_WANDERINGTRADER_REAPPEARED = "mob.wanderingtrader.reappeared";
    public const SOUND_MOB_WITCH_AMBIENT = "mob.witch.ambient";
    public const SOUND_MOB_WITCH_CELEBRATE = "mob.witch.celebrate";
    public const SOUND_MOB_WITCH_DEATH = "mob.witch.death";
    public const SOUND_MOB_WITCH_HURT = "mob.witch.hurt";
    public const SOUND_MOB_WITCH_DRINK = "mob.witch.drink";
    public const SOUND_MOB_WITCH_THROW = "mob.witch.throw";
    public const SOUND_MOB_WITHER_AMBIENT = "mob.wither.ambient";
    public const SOUND_MOB_WITHER_BREAK_BLOCK = "mob.wither.break_block";
    public const SOUND_MOB_WITHER_DEATH = "mob.wither.death";
    public const SOUND_MOB_WITHER_HURT = "mob.wither.hurt";
    public const SOUND_MOB_WITHER_SHOOT = "mob.wither.shoot";
    public const SOUND_MOB_WITHER_SPAWN = "mob.wither.spawn";
    public const SOUND_MOB_WOLF_BARK = "mob.wolf.bark";
    public const SOUND_MOB_WOLF_DEATH = "mob.wolf.death";
    public const SOUND_MOB_WOLF_GROWL = "mob.wolf.growl";
    public const SOUND_MOB_WOLF_HURT = "mob.wolf.hurt";
    public const SOUND_MOB_WOLF_PANTING = "mob.wolf.panting";
    public const SOUND_MOB_WOLF_SHAKE = "mob.wolf.shake";
    public const SOUND_MOB_WOLF_STEP = "mob.wolf.step";
    public const SOUND_MOB_WOLF_WHINE = "mob.wolf.whine";
    public const SOUND_MOB_OCELOT_IDLE = "mob.ocelot.idle";
    public const SOUND_MOB_OCELOT_DEATH = "mob.ocelot.death";
    public const SOUND_MOB_CAT_EAT = "mob.cat.eat";
    public const SOUND_MOB_CAT_HISS = "mob.cat.hiss";
    public const SOUND_MOB_CAT_HIT = "mob.cat.hit";
    public const SOUND_MOB_CAT_MEOW = "mob.cat.meow";
    public const SOUND_MOB_CAT_BEG = "mob.cat.beg";
    public const SOUND_MOB_CAT_STRAYMEOW = "mob.cat.straymeow";
    public const SOUND_MOB_CAT_PURR = "mob.cat.purr";
    public const SOUND_MOB_CAT_PURREOW = "mob.cat.purreow";
    public const SOUND_MOB_POLARBEAR_BABY_IDLE = "mob.polarbear_baby.idle";
    public const SOUND_MOB_POLARBEAR_IDLE = "mob.polarbear.idle";
    public const SOUND_MOB_POLARBEAR_STEP = "mob.polarbear.step";
    public const SOUND_MOB_POLARBEAR_WARNING = "mob.polarbear.warning";
    public const SOUND_MOB_POLARBEAR_HURT = "mob.polarbear.hurt";
    public const SOUND_MOB_POLARBEAR_DEATH = "mob.polarbear.death";
    public const SOUND_MOB_PANDA_BABY_IDLE = "mob.panda_baby.idle";
    public const SOUND_MOB_PANDA_IDLE = "mob.panda.idle";
    public const SOUND_MOB_PANDA_IDLE_AGGRESSIVE = "mob.panda.idle.aggressive";
    public const SOUND_MOB_PANDA_IDLE_WORRIED = "mob.panda.idle.worried";
    public const SOUND_MOB_PANDA_STEP = "mob.panda.step";
    public const SOUND_MOB_PANDA_PRESNEEZE = "mob.panda.presneeze";
    public const SOUND_MOB_PANDA_SNEEZE = "mob.panda.sneeze";
    public const SOUND_MOB_PANDA_HURT = "mob.panda.hurt";
    public const SOUND_MOB_PANDA_DEATH = "mob.panda.death";
    public const SOUND_MOB_PANDA_BITE = "mob.panda.bite";
    public const SOUND_MOB_PANDA_EAT = "mob.panda.eat";
    public const SOUND_MOB_PANDA_CANT_BREED = "mob.panda.cant_breed";
    public const SOUND_MOB_ZOMBIE_DEATH = "mob.zombie.death";
    public const SOUND_MOB_ZOMBIE_HURT = "mob.zombie.hurt";
    public const SOUND_MOB_ZOMBIE_REMEDY = "mob.zombie.remedy";
    public const SOUND_MOB_ZOMBIE_UNFECT = "mob.zombie.unfect";
    public const SOUND_MOB_ZOMBIE_SAY = "mob.zombie.say";
    public const SOUND_MOB_ZOMBIE_STEP = "mob.zombie.step";
    public const SOUND_MOB_ZOMBIE_WOOD = "mob.zombie.wood";
    public const SOUND_MOB_ZOMBIE_WOODBREAK = "mob.zombie.woodbreak";
    public const SOUND_MOB_ZOMBIEPIG_ZPIG = "mob.zombiepig.zpig";
    public const SOUND_MOB_ZOMBIEPIG_ZPIGANGRY = "mob.zombiepig.zpigangry";
    public const SOUND_MOB_ZOMBIEPIG_ZPIGDEATH = "mob.zombiepig.zpigdeath";
    public const SOUND_MOB_ZOMBIEPIG_ZPIGHURT = "mob.zombiepig.zpighurt";
    public const SOUND_MOB_ZOMBIE_VILLAGER_SAY = "mob.zombie_villager.say";
    public const SOUND_MOB_ZOMBIE_VILLAGER_DEATH = "mob.zombie_villager.death";
    public const SOUND_MOB_ZOMBIE_VILLAGER_HURT = "mob.zombie_villager.hurt";
    public const SOUND_NOTE_BANJO = "note.banjo";
    public const SOUND_NOTE_BASS = "note.bass";
    public const SOUND_NOTE_BASSATTACK = "note.bassattack";
    public const SOUND_NOTE_BD = "note.bd";
    public const SOUND_NOTE_BELL = "note.bell";
    public const SOUND_NOTE_BIT = "note.bit";
    public const SOUND_NOTE_COW_BELL = "note.cow_bell";
    public const SOUND_NOTE_DIDGERIDOO = "note.didgeridoo";
    public const SOUND_NOTE_FLUTE = "note.flute";
    public const SOUND_NOTE_GUITAR = "note.guitar";
    public const SOUND_NOTE_HARP = "note.harp";
    public const SOUND_NOTE_HAT = "note.hat";
    public const SOUND_NOTE_CHIME = "note.chime";
    public const SOUND_NOTE_IRON_XYLOPHONE = "note.iron_xylophone";
    public const SOUND_NOTE_PLING = "note.pling";
    public const SOUND_NOTE_SNARE = "note.snare";
    public const SOUND_NOTE_XYLOPHONE = "note.xylophone";
    public const SOUND_PORTAL_PORTAL = "portal.portal";
    public const SOUND_PORTAL_TRAVEL = "portal.travel";
    public const SOUND_PORTAL_TRIGGER = "portal.trigger";
    public const SOUND_RANDOM_ANVIL_BREAK = "random.anvil_break";
    public const SOUND_RANDOM_ANVIL_LAND = "random.anvil_land";
    public const SOUND_RANDOM_ANVIL_USE = "random.anvil_use";
    public const SOUND_RANDOM_BOW = "random.bow";
    public const SOUND_RANDOM_BOWHIT = "random.bowhit";
    public const SOUND_RANDOM_BREAK = "random.break";
    public const SOUND_RANDOM_BURP = "random.burp";
    public const SOUND_RANDOM_CHESTCLOSED = "random.chestclosed";
    public const SOUND_RANDOM_CHESTOPEN = "random.chestopen";
    public const SOUND_RANDOM_SHULKERBOXCLOSED = "random.shulkerboxclosed";
    public const SOUND_RANDOM_SHULKERBOXOPEN = "random.shulkerboxopen";
    public const SOUND_RANDOM_ENDERCHESTOPEN = "random.enderchestopen";
    public const SOUND_RANDOM_ENDERCHESTCLOSED = "random.enderchestclosed";
    public const SOUND_RANDOM_POTION_BREWED = "random.potion.brewed";
    public const SOUND_RANDOM_CLICK = "random.click";
    public const SOUND_RANDOM_DOOR_CLOSE = "random.door_close";
    public const SOUND_RANDOM_DOOR_OPEN = "random.door_open";
    public const SOUND_RANDOM_DRINK = "random.drink";
    public const SOUND_RANDOM_DRINK_HONEY = "random.drink_honey";
    public const SOUND_RANDOM_EAT = "random.eat";
    public const SOUND_RANDOM_EXPLODE = "random.explode";
    public const SOUND_RANDOM_FIZZ = "random.fizz";
    public const SOUND_RANDOM_FUSE = "random.fuse";
    public const SOUND_RANDOM_GLASS = "random.glass";
    public const SOUND_RANDOM_LEVELUP = "random.levelup";
    public const SOUND_RANDOM_ORB = "random.orb";
    public const SOUND_RANDOM_POP = "random.pop";
    public const SOUND_RANDOM_POP2 = "random.pop2";
    public const SOUND_RANDOM_SCREENSHOT = "random.screenshot";
    public const SOUND_RANDOM_SPLASH = "random.splash";
    public const SOUND_RANDOM_SWIM = "random.swim";
    public const SOUND_RANDOM_HURT = "random.hurt";
    public const SOUND_RANDOM_TOAST = "random.toast";
    public const SOUND_RANDOM_TOTEM = "random.totem";
    public const SOUND_CAMERA_TAKE_PICTURE = "camera.take_picture";
    public const SOUND_USE_LADDER = "use.ladder";
    public const SOUND_HIT_LADDER = "hit.ladder";
    public const SOUND_FALL_LADDER = "fall.ladder";
    public const SOUND_STEP_LADDER = "step.ladder";
    public const SOUND_USE_CLOTH = "use.cloth";
    public const SOUND_HIT_CLOTH = "hit.cloth";
    public const SOUND_FALL_CLOTH = "fall.cloth";
    public const SOUND_STEP_CLOTH = "step.cloth";
    public const SOUND_USE_GRASS = "use.grass";
    public const SOUND_HIT_GRASS = "hit.grass";
    public const SOUND_FALL_GRASS = "fall.grass";
    public const SOUND_STEP_GRASS = "step.grass";
    public const SOUND_USE_GRAVEL = "use.gravel";
    public const SOUND_HIT_GRAVEL = "hit.gravel";
    public const SOUND_FALL_GRAVEL = "fall.gravel";
    public const SOUND_STEP_GRAVEL = "step.gravel";
    public const SOUND_USE_SAND = "use.sand";
    public const SOUND_HIT_SAND = "hit.sand";
    public const SOUND_FALL_SAND = "fall.sand";
    public const SOUND_STEP_SAND = "step.sand";
    public const SOUND_USE_SLIME = "use.slime";
    public const SOUND_HIT_SLIME = "hit.slime";
    public const SOUND_FALL_SLIME = "fall.slime";
    public const SOUND_STEP_SLIME = "step.slime";
    public const SOUND_USE_SNOW = "use.snow";
    public const SOUND_HIT_SNOW = "hit.snow";
    public const SOUND_FALL_SNOW = "fall.snow";
    public const SOUND_STEP_SNOW = "step.snow";
    public const SOUND_USE_STONE = "use.stone";
    public const SOUND_HIT_STONE = "hit.stone";
    public const SOUND_FALL_STONE = "fall.stone";
    public const SOUND_FALL_EGG = "fall.egg";
    public const SOUND_STEP_STONE = "step.stone";
    public const SOUND_USE_WOOD = "use.wood";
    public const SOUND_HIT_WOOD = "hit.wood";
    public const SOUND_FALL_WOOD = "fall.wood";
    public const SOUND_STEP_WOOD = "step.wood";
    public const SOUND_JUMP_CLOTH = "jump.cloth";
    public const SOUND_JUMP_GRASS = "jump.grass";
    public const SOUND_JUMP_GRAVEL = "jump.gravel";
    public const SOUND_JUMP_SAND = "jump.sand";
    public const SOUND_JUMP_SNOW = "jump.snow";
    public const SOUND_JUMP_STONE = "jump.stone";
    public const SOUND_JUMP_WOOD = "jump.wood";
    public const SOUND_JUMP_SLIME = "jump.slime";
    public const SOUND_LAND_CLOTH = "land.cloth";
    public const SOUND_LAND_GRASS = "land.grass";
    public const SOUND_LAND_GRAVEL = "land.gravel";
    public const SOUND_LAND_SAND = "land.sand";
    public const SOUND_LAND_SNOW = "land.snow";
    public const SOUND_LAND_STONE = "land.stone";
    public const SOUND_LAND_WOOD = "land.wood";
    public const SOUND_LAND_SLIME = "land.slime";
    public const SOUND_VR_STUTTERTURN = "vr.stutterturn";
    public const SOUND_RECORD_13 = "record.13";
    public const SOUND_RECORD_CAT = "record.cat";
    public const SOUND_RECORD_BLOCKS = "record.blocks";
    public const SOUND_RECORD_CHIRP = "record.chirp";
    public const SOUND_RECORD_FAR = "record.far";
    public const SOUND_RECORD_MALL = "record.mall";
    public const SOUND_RECORD_MELLOHI = "record.mellohi";
    public const SOUND_RECORD_STAL = "record.stal";
    public const SOUND_RECORD_STRAD = "record.strad";
    public const SOUND_RECORD_WARD = "record.ward";
    public const SOUND_RECORD_11 = "record.11";
    public const SOUND_RECORD_WAIT = "record.wait";
    public const SOUND_RAID_HORN = "raid.horn";
    public const SOUND_MUSIC_MENU = "music.menu";
    public const SOUND_MUSIC_GAME = "music.game";
    public const SOUND_MUSIC_GAME_CREATIVE = "music.game.creative";
    public const SOUND_MUSIC_GAME_END = "music.game.end";
    public const SOUND_MUSIC_GAME_ENDBOSS = "music.game.endboss";
    public const SOUND_MUSIC_GAME_NETHER = "music.game.nether";
    public const SOUND_MUSIC_GAME_CREDITS = "music.game.credits";
    public const SOUND_MOB_BEE_AGGRESSIVE = "mob.bee.aggressive";
    public const SOUND_MOB_BEE_DEATH = "mob.bee.death";
    public const SOUND_MOB_BEE_HURT = "mob.bee.hurt";
    public const SOUND_MOB_BEE_LOOP = "mob.bee.loop";
    public const SOUND_MOB_BEE_POLLINATE = "mob.bee.pollinate";
    public const SOUND_MOB_BEE_STING = "mob.bee.sting";
    public const SOUND_BLOCK_BEEHIVE_ENTER = "block.beehive.enter";
    public const SOUND_BLOCK_BEEHIVE_EXIT = "block.beehive.exit";
    public const SOUND_BLOCK_BEEHIVE_SHEAR = "block.beehive.shear";
    public const SOUND_BLOCK_BEEHIVE_WORK = "block.beehive.work";
    public const SOUND_BLOCK_BEEHIVE_DRIP = "block.beehive.drip";
}