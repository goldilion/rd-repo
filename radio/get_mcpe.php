<?php
include("includes/db_connection.php");

/*
com.godisk.mcpeskycity
com.godisk.mcpepocketdecoration
com.godisk.mcpestainedglass
com.godisk.mcpefurniture
com.godisk.mcpehideandseek
com.godisk.mcpegunsmod
com.godisk.mcpeweaponcase
com.godisk.mcpesuperhero
com.godisk.mcpeportal
com.godisk.mcpemodernhouses
com.godisk.mcpefortnite
com.godisk.mcpethemepark
com.godisk.mcpenintendo
com.godisk.mcpelavarun
com.godisk.mcpeluckyblock
com.godisk.mcpegolemworld
com.godisk.mcpearmor
com.godisk.mcpeinventorypets
com.godisk.mcpeblokkit
com.godisk.mcpedmgindicator
*/

$arr = array("maps.formcpe.skycity",
"mods.formcpe.pocketdecoration",
"mods.formcpe.stainedglass",
"mods.formcpe.furniture",
"maps.formcpe.hideandseek",
"mods.formcpe.gunsmod",
"mods.formcpe.weaponcase",
"mods.formcpe.superhero",
"mods.formcpe.portal",
"maps.formcpe.modernhouses",
"maps.formcpe.fortnite",
"maps.formcpe.themepark",
"maps.formcpe.nintendo",
"maps.formcpe.lavarun",
"mods.formcpe.luckyblock",
"mods.formcpe.golemworld",
"mods.formcpe.armor",
"mods.formcpe.inventorypets",
"mods.formcpe.blokkit",
"mods.formcpe.dmgindicator");


	echo '
    <form method="get" action="">
    App: <select name="app">
        ';
    	
    	foreach ($arr as $val) {
           echo '<option value="'.$val.'">'.$val.'</option>';
        }
        
	echo '
    </select>
    <br />
    Save Images Only <input type="checkbox" name="saveimg" value="1">
    <br />
    <input type="submit" name="submit" value="Grab">
    </form>
    <br />
    <a href="get_mcpe.php">Back to Home</a>
    <br />
    <br />
    ';

if(isset($_GET['submit'])) {
	getMCPEList($_GET['app'], $_GET['saveimg'], $connect);
}

function getMCPEList($app, $saveimg, $connect) {

	$request = "http://128.199.40.174:8090/api/v1/addons";

	$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-BUNDLE: '.$app,
			'X-VERSION: 9',
			'X-VERSION-NAME: 2.3.9',
			'X-LOCALE: EN',
			'X-DEVICE: 1',
			'X-MODEL: 1',
			'X-PRODUCT: 1',
			'X-APPODEAL-SDK: 2.1.11'
			));
		$response = curl_exec($ch);
	$getmcpe = json_decode($response, true);
	
    foreach ($getmcpe['data'] as $data) {
        $id = $data['id'];
        $title = $connect->quote($data['title_en']);
        $type = $data['type'];
        $desc = $connect->quote($data['description']);
        $img = $data['images'][0]['url'];
        $file = $data['files'][0]['url'];
        //print_r($data);

        if($_GET['saveimg'] == '1') {
            $j = 0;
            foreach ($data['images'] as $data2) {
                $no = $j++;
                echo $data2['url'];
                echo '<br />';
                $img_file = slugify($app)."_".slugify($id)."_".$no.".png";
                file_put_contents( 'img_mcpe/'.$img_file , file_get_contents( $data2['url'] ) ); 
            }
        } else {
            $sql_insert = "INSERT INTO tbl_mcpe (id, cat_id, game_id, game_app_ref, game_app_ref_enc, game_title, game_url, game_thumbnail, game_desc, game_type, is_deleted, last_updated)
                        VALUES (NULL, '1', '".$id."', '".$app."', '".md5('*'.$app.'*')."', ".$title.", '".$file."', '".$img."', ".$desc.", '".$type."', '0', NOW());
                        ";

            echo $sql_insert;
            echo '<br />';
            #$connect->exec($sql_insert);
        }
      

    }
}

function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

/*
{
    "total": 30,
    "per_page": 5,
    "current_page": 1,
    "last_page": 6,
    "next_page_url": "http://128.199.40.174:8090/api/v1/initial-request?page=2",
    "prev_page_url": null,
    "from": 1,
    "to": 5,
    "data": [
        {
            "id": 2489,
            "title_en": "Dan’s Furniture Mod for Minecraft PE 0.12.1",
            "title_ru": "Мебель мод Дэна для Майнкрафт пе 0.12.1",
            "description_en": "Dan’s Furniture Mod was inspired by furniture in real life.They include many furniture and items in the game.I may list up to seventeen different types.The Mod gives you a much wider variety with mobs in Minecraft PE. If you’ve grown tired of the old types of furniture and you’ve learned all about them, then this mod is for you.\nFurniture used to primarily to decorate your home in Minecraft PE. There are both modern decorations such as a computer and some ordinary furniture like a chair, a table, chests, fences and much more.It’s very easy to use.\nDan’s FurnitureDan’s FurnitureDan’s FurnitureDan’s FurnitureAuthor:DanHerePE\nDownload: ",
            "description_ru": "Мебель мод Дэна был вдохновлен мебелью в реальной жизни.Они включают множество мебели и предметов в игре.Я могу список до семнадцати различных видов.Мод дает вам гораздо более широкий круг с мобами в Майнкрафт пе. Если вы устали от старой мебели, и ты узнаешь все о них, то этот мод для вас.\nМебель, используемая в первую очередь, чтобы украсить свой дом в Майнкрафт пе. Есть как современные украшения, такие как компьютерная и обычная мебель, как стул, стол, сундуки, заборы и многое другое.Это очень простой в использовании.\nFurnitureAuthor FurnitureDan FurnitureDan FurnitureDan Дэна s:в DanHerePE\nСкачать: ",
            "type": "mod",
            "title": "Dan’s Furniture Mod for Minecraft PE 0.12.1",
            "description": "Dan’s Furniture Mod was inspired by furniture in real life.They include many furniture and items in the game.I may list up to seventeen different types.The Mod gives you a much wider variety with mobs in Minecraft PE. If you’ve grown tired of the old types of furniture and you’ve learned all about them, then this mod is for you.\nFurniture used to primarily to decorate your home in Minecraft PE. There are both modern decorations such as a computer and some ordinary furniture like a chair, a table, chests, fences and much more.It’s very easy to use.\nDan’s FurnitureDan’s FurnitureDan’s FurnitureDan’s FurnitureAuthor:DanHerePE\nDownload: ",
            "images": [
                {
                    "url": "http://mcpe.review:7788/image/8807-dans-furniture-mod-1.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8808-dans-furniture-mod-2.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8809-dans-furniture-mod-3.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8810-dans-furniture-mod-4.jpg"
                }
            ],
            "files": [
                {
                    "id": 5717,
                    "url": "http://mcpe.review:7788/mod/2489-manual-mine-furniture-packs-v3.zip"
                }
            ]
        },
        {
            "id": 2483,
            "title_en": "Electrics Furniture Mod for Minecraft PE 0.12.1",
            "title_ru": "Электрика мебель мод для Майнкрафт PE 0.12.1",
            "description_en": "In Minecraft PE, there are many mods about furniture.ElectricsFurniture Mod provides you furniture about things in kitchens room and living room. here.\nYou will get a whole library, or kitchen, or bathroom.This mod provides you five types of furniture in game.It was on your inventory.Your task is that arrange them such that look beautiful.Doing it in your way. It’s an ingenious design scheme that most players like as soon as they realize what’s up.Because it is the first version about furniture in kitchen room, living room,..furniture in the mod are simple.I certainly that it will complete in the future.\nFurniture IDs:\nSmall Wooden Table (250)\nSmall Wooden Stool (251)\nSmall Glass Table (252)\nOven (253)\nWooden Chair (254)\nElectrics FurnitureElectrics FurnitureElectrics FurnitureAuthor:ElectricGamer67\nDownload:",
            "description_ru": "В Майнкрафт пе, есть много модов про мебель.Мод ElectricsFurniture предоставляет Вам мебель в номере кухни и гостиной. здесь.\nВы получите целую библиотеку, или кухня, или ванная комната.Этот мод предоставляет Вам пять типов мебели в игру.Он был на своем инвентаре.Ваша задача заключается в том, что организовать их таким образом, что выглядят красиво.Делаю на вашем пути. Это хитроумные схемы, что большинство игроков, как только они поймут, что случилось.Потому что это первая версия про мебель в кухне, гостиной, мебель..в мод не простой.Я, конечно, что она будет завершена в будущем.\nМебель Идентификаторы:\nНебольшой Деревянный Стол (250)\nНебольшой Деревянный Табурет (251)\nНебольшой Стеклянный Стол (252)\nДуховка (253)\nДеревянный Стул (254)\nЭлектрика FurnitureElectrics FurnitureElectrics FurnitureAuthor:ElectricGamer67\nСкачать:",
            "type": "mod",
            "title": "Electrics Furniture Mod for Minecraft PE 0.12.1",
            "description": "In Minecraft PE, there are many mods about furniture.ElectricsFurniture Mod provides you furniture about things in kitchens room and living room. here.\nYou will get a whole library, or kitchen, or bathroom.This mod provides you five types of furniture in game.It was on your inventory.Your task is that arrange them such that look beautiful.Doing it in your way. It’s an ingenious design scheme that most players like as soon as they realize what’s up.Because it is the first version about furniture in kitchen room, living room,..furniture in the mod are simple.I certainly that it will complete in the future.\nFurniture IDs:\nSmall Wooden Table (250)\nSmall Wooden Stool (251)\nSmall Glass Table (252)\nOven (253)\nWooden Chair (254)\nElectrics FurnitureElectrics FurnitureElectrics FurnitureAuthor:ElectricGamer67\nDownload:",
            "images": [
                {
                    "url": "http://mcpe.review:7788/image/8782-electrics-furniture-mod-1.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8783-electrics-furniture-mod-2.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8784-electrics-furniture-mod-3.jpg"
                }
            ],
            "files": [
                {
                    "id": 5716,
                    "url": "http://mcpe.review:7788/mod/2483-manual-ElectricsFurnitureAddonV1.zip"
                }
            ]
        },
        {
            "id": 2340,
            "title_en": "Pocket Decoration Mod for Minecraft PE 0.13.0",
            "title_ru": "Мод Pocket Decoration для Майнкрафт пе 0.13.0",
            "description_en": "Pocket Decoration Mod is a colorful and great mod which is about furniture and decoration.This mod was created by VCraft who is a new author in Minecraft PE.With this mod, you can decorate anything.It provides you so much new furniture and  glass blocks.Estimated around sixteen so each type.For example laptop, new chairs, new tables, kitchen furniture and more.Your house will look great and far more modern.You can craft for having them or they are in your creative inventory.if you are in creative mode.Item/Block IDs & Crafting Recipes\r\nBin (601) – 6 iron ingots + 1 stone block\r\nCabinet (602) – 8 wooden blocks + 1 chest\r\nChopping Board (603) – 6 oak wood logs + 3 wooden slabs\r\nComputer (604) – 7 iron blocks + 1 glass pane + 1 redstone\r\nDishwasher (605) – 9 quartz blocks\r\nFridge (606) – 7 iron blocks + 1 chest + 1 ice block\r\nKitchen Cabinet (607) – 6 quartz blocks + 2 cyan stained clay + 1 chest\r\nKitchen Counter (608) – 3 cyan stained clay + 6 quartz blocks\r\nMicrowave (609) – 6 quartz blocks + 2 glass panes + 1 stone block\r\nOven (610) – 8 iron blocks + 1 chest\r\nOven Top (611) – 4 iron ingots + 1 glowstone\r\nStereo (612) – 1 iron ingot + 3 acacia wood blocks\r\nStone Path (613) – 3 diorite blocks\r\nStone Table (614) – 5 stone blocks\r\nTelevision (615) – 7 acacia wood logs + 1 glass pane + 1 redstone\r\nWashing Machine (616) – 7 quartz blocks + 1 glass pane + 1 anvil\r\nWooden Table (617) – 5 oak wood planks.\r\nWhite Stained Glass (223)\r\nOrange Stained Glass (224)\r\nMagenta Stained Glass (225)\r\nLight Blue Stained Glass (226)\r\nYellow Stained Glass (227)\r\nLime Stained Glass (228)\r\nPink Stained Glass (229)\r\nGray Stained Glass (230)\r\nLight Gray Stained Glass (231)\r\nCyan Stained Class (232)\r\nPurple Stained Glass (233)\r\nBlue Stained Glass (234)\r\nBrown Stained Glass (235)\r\nGreen Stained Glass (236)\r\nRed Stained Glass (237)\r\nBlack Stained Glass (238)\r\nAuthor:VCraft\r\nDownload",
            "description_ru": "Карманный мода украшения красочный и большой мод, который является о мебели и декора.Этот мод был создан VCraft, который является новый Автор в Майнкрафт пе.С этим модом вы сможете украсить что-нибудь.Он предоставляет Вам так много новой мебели и стеклянных блоков.По оценкам, около шестнадцати, так что каждый Тип.Например ноутбук, новые стулья, новые столы, кухонная мебель и многое другое.Ваш дом будет отлично выглядеть и гораздо более современные.Вы можете создавать для них или они в вашем творческом инвентаре.если вы находитесь в творческом режиме.Элемент/Блок Преимуществ С Модом\r\nБин (601) – 6 железных слитков + 1 каменный блок\r\nШкаф (602) – 8 деревянных блоков + 1 сундук\r\nРазделочная доска (603) – 6 дубовых деревянных бревен + 3 деревянных плиты\r\nКомпьютер (604) – 7 железных блоков + 1 стеклянная панель + 1 редстоун\r\nПосудомоечная машина (605) – 9 кварцевых блоков\r\nХолодильник (606) – 7 железных блоков + 1 сундук + 1 блок льда\r\nКухонный шкаф (607) – 6 кварцевых блоков + 2 голубой в пятнах глины + 1 сундук\r\nКухонный стол (608) – 3 тонированный голубой глины + 6 кварцевых блоков\r\nМикроволновая печь (609) – 6 кварцевых блоков + 2 стеклянные панели + 1 каменный блок\r\nДуховка (610) – 8 железных блоков + 1 сундук\r\nВерхняя печь (611) – 4 железных слитков + 1 glowstone\r\nСтерео (612) – 1 железный слиток + 3 дерева ситтим блоков\r\nКаменная дорожка (613) – 3-диоритовые блоки\r\nКаменный стол (614) – 5 каменных блоков\r\nТелевизор (615) – 7 акации бревен + 1 стекло + 1 редстоун\r\nСтиральная машина (616) – 7 кварцевых блоков + 1 стеклянная панель + 1 контрнож\r\nДеревянный стол (617) – 5 досок дуба.\r\nБелый Витраж (223)\r\nОранжевый Витраж (224)\r\nПурпурный Витраж (225)\r\nСветло-Голубой Витраж (226)\r\nЖелтый Витраж (227)\r\nЛайм Витраж (228)\r\nРозовый Витраж (229)\r\nСерый Витраж (230)\r\nСветло-Серый Витражи (231)\r\nГолубой Тонированный Класса (232)\r\nФиолетовый Витраж (233)\r\nСиний Витраж (234)\r\nКоричневый Витраж (235)\r\nЗеленый Витражного Стекла (236)\r\nКрасный Витраж (237)\r\nЧерный Витраж (238)\r\nАвтор:VCraft\r\nСкачать",
            "type": "mod",
            "title": "Pocket Decoration Mod for Minecraft PE 0.13.0",
            "description": "Pocket Decoration Mod is a colorful and great mod which is about furniture and decoration.This mod was created by VCraft who is a new author in Minecraft PE.With this mod, you can decorate anything.It provides you so much new furniture and  glass blocks.Estimated around sixteen so each type.For example laptop, new chairs, new tables, kitchen furniture and more.Your house will look great and far more modern.You can craft for having them or they are in your creative inventory.if you are in creative mode.Item/Block IDs & Crafting Recipes\r\nBin (601) – 6 iron ingots + 1 stone block\r\nCabinet (602) – 8 wooden blocks + 1 chest\r\nChopping Board (603) – 6 oak wood logs + 3 wooden slabs\r\nComputer (604) – 7 iron blocks + 1 glass pane + 1 redstone\r\nDishwasher (605) – 9 quartz blocks\r\nFridge (606) – 7 iron blocks + 1 chest + 1 ice block\r\nKitchen Cabinet (607) – 6 quartz blocks + 2 cyan stained clay + 1 chest\r\nKitchen Counter (608) – 3 cyan stained clay + 6 quartz blocks\r\nMicrowave (609) – 6 quartz blocks + 2 glass panes + 1 stone block\r\nOven (610) – 8 iron blocks + 1 chest\r\nOven Top (611) – 4 iron ingots + 1 glowstone\r\nStereo (612) – 1 iron ingot + 3 acacia wood blocks\r\nStone Path (613) – 3 diorite blocks\r\nStone Table (614) – 5 stone blocks\r\nTelevision (615) – 7 acacia wood logs + 1 glass pane + 1 redstone\r\nWashing Machine (616) – 7 quartz blocks + 1 glass pane + 1 anvil\r\nWooden Table (617) – 5 oak wood planks.\r\nWhite Stained Glass (223)\r\nOrange Stained Glass (224)\r\nMagenta Stained Glass (225)\r\nLight Blue Stained Glass (226)\r\nYellow Stained Glass (227)\r\nLime Stained Glass (228)\r\nPink Stained Glass (229)\r\nGray Stained Glass (230)\r\nLight Gray Stained Glass (231)\r\nCyan Stained Class (232)\r\nPurple Stained Glass (233)\r\nBlue Stained Glass (234)\r\nBrown Stained Glass (235)\r\nGreen Stained Glass (236)\r\nRed Stained Glass (237)\r\nBlack Stained Glass (238)\r\nAuthor:VCraft\r\nDownload",
            "images": [
                {
                    "url": "http://mcpe.review:7788/image/8354-pocketdecoration-mod1.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8355-pocketdecoration-mod2.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8356-pocketdecoration-mod3.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8357-pocketdecoration-mod4.jpg"
                }
            ],
            "files": [
                {
                    "id": 5647,
                    "url": "http://mcpe.review:7788/mod/2340-manual-PocketDecoration-Mod.zip"
                }
            ]
        },
        {
            "id": 2289,
            "title_en": "Toolbox Mod for Minecraft PE 0.12.0/0.12.1",
            "title_ru": "Мод Toolbox для minecraft пе 0.12.0/0.12.1",
            "description_en": "Toolbox Mod is the most important mod in Minecraft PE version 0.12.x .Because from now on, the later mods must use Toolbox Mod.It will help you create everything if you need.It can access all types of blocks, items and furnitures and changing settings in-game such as the weather, the game mode and much more ….It’s very useful and solve problems very fast.\n Features:\nAccess all existing items & blocks\nEnchanting system for items\nSwitch between creative/survival, Overworld/Nether, flying\nKill, heal, set health (1-100 HP), restore hunger\nSet spawn position & teleport there\nSet time of day, turn off time, disable ticking\nWeather (no rain/rain/much rain)\nPotion effects: remove all, add\nRemove items from inventory\nEntities: spawn, kill, set on fire, set health\nShow/hide M button\nTeleportation system with screenshots\nToolboxToolboxToolboxToolboxToolboxInstall Guide:\nDownload the .APK.\nUse ES File Manager (or any other file manager app) to locate the downloaded file (toolbox mod.apk).\nTap on the file to install it (Block launcher is required to successfully install the addon).\nAfter you’ve finished the installation it’s done and you can go in-game to use Toolbox!\nAuthor: MCMrArm\nDownload:",
            "description_ru": "Мод Toolbox-это самый важный мод в Minecraft PE версии 0.12.х .Потому что отныне позже модов должны использовать мод элементов.Это поможет вам создать все, если вам нужно.Он может открыть все виды блоков, предметов и мебели, изменять настройки в игре, такие как погода, режим игры и многое другое ....Это очень полезно и очень быстро решать проблемы.\nОсобенности:\nДоступ всех существующих предметов и блоков\nФеерично системы для пользования\nПереключение между творческий/выживания, Верхний/Нижний, летающая\nУбить, излечить, комплекс здоровье (1-100 л. с.), восстановить голод\nУстановить спавн позицию и телепортироваться туда\nУстановите время, выключите времени, отключить тикают\nПогода (без дождя/дождь/дождь)\nЭффекты зелий: удалить все, добавить\nУдалить предметы из инвентаря\nЛиц: спаун, убил, поджег, поставил здравоохранения\nПоказать/Скрыть кнопку М\nСистема телепортации со скриншотами\nРуководство ToolboxToolboxToolboxToolboxToolboxinstall:\nСкачать .АПК.\nИспользовать ES Диспетчер файлов (или любого другого файлового менеджера приложение), чтобы найти загруженный файл (мод элементов.АПК).\nНажмите на файл, чтобы установить его (блок лаунчер необходим для установки аддона).\nПосле окончания установки это сделано, и вы можете пойти в игру, чтобы использовать набор инструментов!\nАвтор: MCMrArm\nСкачать:",
            "type": "mod",
            "title": "Toolbox Mod for Minecraft PE 0.12.0/0.12.1",
            "description": "Toolbox Mod is the most important mod in Minecraft PE version 0.12.x .Because from now on, the later mods must use Toolbox Mod.It will help you create everything if you need.It can access all types of blocks, items and furnitures and changing settings in-game such as the weather, the game mode and much more ….It’s very useful and solve problems very fast.\n Features:\nAccess all existing items & blocks\nEnchanting system for items\nSwitch between creative/survival, Overworld/Nether, flying\nKill, heal, set health (1-100 HP), restore hunger\nSet spawn position & teleport there\nSet time of day, turn off time, disable ticking\nWeather (no rain/rain/much rain)\nPotion effects: remove all, add\nRemove items from inventory\nEntities: spawn, kill, set on fire, set health\nShow/hide M button\nTeleportation system with screenshots\nToolboxToolboxToolboxToolboxToolboxInstall Guide:\nDownload the .APK.\nUse ES File Manager (or any other file manager app) to locate the downloaded file (toolbox mod.apk).\nTap on the file to install it (Block launcher is required to successfully install the addon).\nAfter you’ve finished the installation it’s done and you can go in-game to use Toolbox!\nAuthor: MCMrArm\nDownload:",
            "images": [
                {
                    "url": "http://mcpe.review:7788/image/8214-toolbox-mod-1.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8215-toolbox-mod-2.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8216-toolbox-mod-3.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8217-toolbox-mod-4.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/8218-toolbox-mod-5.jpg"
                }
            ],
            "files": [
                {
                    "id": 5483,
                    "url": "http://mcpe.review:7788/mod/2289-manual-toolbox-mod.zip"
                }
            ]
        },
        {
            "id": 2145,
            "title_en": "Pocket Furniture Mod for Minecraft PE 0.10.0",
            "title_ru": "Лучший мебель мод для Майнкрафт пе 0.10.0",
            "description_en": "Pocket Furniture Mod is a creative and cool mod which is about furniture in Minecraft PE.With this mod, you will have many new items which are furniture in your house.Such as chair, stone path, table, TV, cupboard.These items may not be manipulated in this version of the mod and therefore, you must use the following command to get the items: / Give.I am sure this is necessary for your mod\r\nPocket FurniturePocket FurniturePocket FurnitureDownload",
            "description_ru": "Карманный мебели мод-это творческий и крутой мод, который про мебель в Майнкрафт пе.С этим модом у вас появится много новых предметов, которые мебель в своем доме.Например, стул, каменная дорожка, столик, телевизор, шкаф.Эти предметы не могут использоваться в этой версии мода и таким образом, вы должны использовать следующую команду, чтобы получить элементы: / дал.Я уверен, что это необходимо для вашего мода\r\nКарман FurniturePocket FurniturePocket FurnitureDownload",
            "type": "mod",
            "title": "Pocket Furniture Mod for Minecraft PE 0.10.0",
            "description": "Pocket Furniture Mod is a creative and cool mod which is about furniture in Minecraft PE.With this mod, you will have many new items which are furniture in your house.Such as chair, stone path, table, TV, cupboard.These items may not be manipulated in this version of the mod and therefore, you must use the following command to get the items: / Give.I am sure this is necessary for your mod\r\nPocket FurniturePocket FurniturePocket FurnitureDownload",
            "images": [
                {
                    "url": "http://mcpe.review:7788/image/7910-pocket-furniture-mod1.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/7911-pocket-furniture-mod2.jpg"
                },
                {
                    "url": "http://mcpe.review:7788/image/7912-pocket-furniture-mod3.jpg"
                }
            ],
            "files": [
                {
                    "id": 5713,
                    "url": "http://mcpe.review:7788/mod/2145-manual-Pocket-Furniture-Mod.zip"
                }
            ]
        }
    ]
}
*/

?>