#!/usr/bin/env python3
import router  # Custom library that has router controls
import urllib.request
try:  # Will check if device supports GPIO otherwise will print/log statements
    import RPi.GPIO as GPIO

    skipGPIO = False
except:
    skipGPIO = True
import json

import time
import traceback
import os
import datetime
import sys
import glob
import string
whitelist = set(string.ascii_letters + string.digits + "/" + "@" + "." + "-" + "_")

def sanitize(txt): # Is a very simple sanitizer that allows all ascii_letters numbers and the / and @
    if type(txt) == type(1) or type(txt) == type(True): # Makes sure that if it is an int or a bool it will not be sanitized because that is not neccessary.
        return txt
    else:
        return ''.join([ch for ch in txt if ch in whitelist])

def error(e):
    return "".join(traceback.format_exception(etype=type(e), value=e, tb=e.__traceback__))


def temp(): # Returns the temprature of the RPI
    return readFile("/sys/class/thermal/thermal_zone0/temp") / 1000


def writeFile(location, info, permission=True):  # Will write info in json format to a file
    with open(location, "w") as f:
        json.dump(info, f)
        if permission:
            os.system("chown -R www-data:www-data " + location)


# Loads the location of a certain file and returns that file if it is json
def readFile(location):
    with open(location) as f:
        return json.load(f)

try:
    # Looks at the configuration at understands the config
    try:
        configFilePath = __file__[: __file__.rindex("/") + 1]
        # Makes sure the python file area is owned by root and not accessable by www-data for security reasons
        os.system("chmod 750 -R " + configFilePath)
        os.system("chown -R root:root " + configFilePath)
        configFilePath = configFilePath + "config.json"
        # Will find the location where the config should be located.
        location = __file__[: __file__.rindex("/python/restart.py") + 1] + "html/"
        # Creates config with the enviromental variables
        if os.getenv("WEBSITE_DEVELOPER", "false") == "true":
            developmentMachine = True
        else:
            developmentMachine = False
        # This stores a list for the default config
        envConfiguration = [
            [["passwordOptions", "cost"], int(os.getenv("PASSWORD_ROUNDS", '10'))],
            [["api"], os.getenv("WEBSITE_API")],
            [["mail", "server"], os.getenv("MAIL_SMTP_SERVER", "smtp.sendgrid.net")],
            [["mail", "username"], os.getenv("MAIL_USERNAME", "apikey")],
            [["mail", "password"], os.getenv("MAIL_PASSWORD", "none")],
            [["mail", "port"], int(os.getenv("MAIL_SMTP_PORT", "587"))],
            [["database", "username"], os.getenv("WEBSITE_USER", "admin")],
            [["database", "name"], os.getenv("WEBSITE_DATABASE_TABLE", "website")],
            [["database", "password"], os.getenv("WEBSITE_PASSWORD", "password")],
            [["database", "backupLocation"], os.getenv("WEBSITE_BACKUP_LOCATION", "/backup")],
            [["database", "backupLength"], int(os.getenv("WEBSITE_BACKUP_LENGTH", "604800"))],
            [["developer"], developmentMachine],
            [["throttle"], int(os.getenv("WEBSITE_THROTTLE", "5"))],
            [["throttleTime"], int(os.getenv("WEBSITE_THROTTLE_TIME", '30'))],
            [["fanStart"], int(os.getenv("WEBSITE_FAN_START", '43'))],
            [["fanStop"], int(os.getenv("WEBSITE_FAN_STOP", '35'))],
            [["matomoDomain"], os.getenv("MATOMO_DOMAIN", 'example.com')],
            [["matomoSiteId"], int(os.getenv("MATOMO_SITE_ID", '1'))],
            [["turnstileSecret"], os.getenv("TURNSTILE_SECRET", '')],
            [["turnstileSitekey"], os.getenv("TURNSTILE_SITEKEY", '')],
            [["TMDBApiKey"], os.getenv("TMDB_API_KEY", '')],
        ]
        if os.path.exists(location + "/config.json"):
            configuration = readFile(location + "/config.json")
        else:
            # Sets an empty config if none is found to be filled with the env vars
            configuration = {}
            print("Setting config with enviromental variables")
        # Goes through config to make sure values are sanitized and that they exist
    except:
        print("Could not create config")
        raise Exception
    # Sanitizes and makes sure that config is complete.
    try:
        for x in envConfiguration:
            if len(x[0]) == 1:
                # Checks if the key value pair exists or if it has to be created and the enviromental variable value has to be used
                if x[0][0] in configuration:
                    configuration[x[0][0]] = sanitize(configuration[x[0][0]])
                else:
                    configuration[x[0][0]] = sanitize(x[1])
            else:
                # Checks if the key value pair exists or if it has to be created and the enviromental variable value has to be used
                if x[0][0] in configuration:
                    if x[0][1] in configuration[x[0][0]]:
                        configuration[x[0][0]][x[0][1]] = sanitize(configuration[x[0][0]][x[0][1]])
                    else:
                        configuration[x[0][0]][x[0][1]] = sanitize(x[1])
                else:
                    configuration[x[0][0]] = {x[0][1] : sanitize(x[1])}
    except:
        print("Failed sanitizing the config")
        raise Exception
    writeFile(f"{location}/config.json", configuration)
    developmentMachine = configuration["developer"]
    backupLocation = configuration["database"]["backupLocation"]
    # Makes sure that the permissions are not wrong
    os.system("chown -R www-data:www-data " + location)
    os.system(f"chmod 644 -R {backupLocation}")
    os.system("chmod 750 -R " + location)
    f = open(location + "/maintenance-mode", "w")
    f.close()
    while (
        True
    ):  # Imports this library in a slow way because it is a pain and likes to not work
        try:
            import database
            break
        except:
            continue
    internetOnDeafult = ["23", "0", "5", "0", "2147483000", "1"]
    internet = False
    if not skipGPIO:
        # Will setup the gpio button
        GPIO.setmode(GPIO.BOARD)
        GPIO.setup(10, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)
        GPIO.setup(8, GPIO.OUT)
        # Will setup the GPIO fan
        GPIO.setup(16, GPIO.OUT)
        GPIO.output(16, GPIO.LOW)

    def writeLog(message, kind):  # Will automatically add to the log
        print(message, time.time())
        try:
            database.appendValue("log", [str(kind), message, str(time.time())])
        except:
            print("Could not log message")

    # Checks if an action is neccessary to do on the wifi

    def internetAction(times, rule, status):
        timeStart = int(rule[0]) * 60 + int(rule[1])
        timeEnd = int(rule[2]) * 60 + int(rule[3])
        times = int(times[3]) * 60 + int(times[4])
        if timeEnd < timeStart:
            if timeEnd > times:
                times += 60 * 24
            timeEnd += 60 * 24
        if times >= timeStart and times <= timeEnd:
            if status:
                if skipGPIO:
                    print("LED turning on")
                else:
                    GPIO.output(8, GPIO.HIGH)
                writeLog("Internet turning off", 6)
                if not developmentMachine:
                    status = router.turnOffInternet()
                else:
                    status = False
                writeLog("Internet turned off", 6)
                if skipGPIO:
                    print("LED turning off")
                else:
                    GPIO.output(8, GPIO.LOW)
        else:
            if not status:
                if skipGPIO:
                    print("LED turning on")
                else:
                    GPIO.output(8, GPIO.HIGH)
                writeLog("Internet turning on", 7)
                if not developmentMachine:
                    status = router.turnOnInternet()
                else:
                    status = True
                writeLog("Internet turned on", 7)
                if skipGPIO:
                    print("LED turning off")
                else:
                    GPIO.output(8, GPIO.LOW)
        return status

    def callTime():  # Will return the time
        time = datetime.datetime.now()
        time2 = [
            time.strftime("%b"),
            time.strftime("%d"),
            time.strftime("%Y"),
            time.strftime("%H"),
            time.strftime("%M"),
            time.strftime("%S"),
            time.strftime("%-m"),
        ]
        return time2

    def buttonPress(status):  # Will run this script everytime the button is pressed
        minimum = database.search(
            "internet", "id=(SELECT MIN(id) FROM internet)")
        minimum = int(minimum[5]) - 1
        if minimum == 0:
            minimum = -1
        if status:
            writeLog("Internet Schedule changed to off due to button", 5)
            database.appendValue(
                "internet",
                ["0", "0", "23", "59", str(time.time() + 3600), str(minimum)],
            )
        else:
            writeLog("Internet Schedule changed to on due to button", 5)
            database.appendValue(
                "internet", ["2", "1", "2", "1", str(
                    time.time() + 3600), str(minimum)]
            )
    def backupDatabase(): # Used to backup the database
        timeData = callTime()
        month = timeData[0]
        day = timeData[1]
        year = timeData[2]
        hour = timeData[3]
        minute = timeData[4]
        file = f"{int(time.time())}or{month}-{day}-{year}at{hour}:{minute}.sql"
        database.backUp(file)
        return file
    def dailyMaintenance():  # Will run daily and on boot
        try:
            if not os.path.isfile(location + "restore.json"): # Makes sure that it does not disrupt a restore
                file = backupDatabase()
                writeLog(f"Ran backup on server and saved it to {file}", 18)
            else:
                writeLog("Skipped backup due to a restore command", 18)
        except:
            writeLog("Database backup failed", 9)
        # Will find all backup files and check them.
        backupLocation = configuration["database"]["backupLocation"]
        files = glob.glob(f"{backupLocation}/*.sql")
        for x in range(len(files)):
            files[x] = files[x][len(backupLocation)+1:]
        for x in files:
            try:
                if int(x[:x.find("or")]) < time.time() - configuration["database"]["backupLength"]:
                    os.remove(f"{backupLocation}/{x}")
                    writeLog(f"Removed old backup due to age, named {x}", 20)
            except:
                os.remove(f"{backupLocation}/{x}")
        # Gets a list of all backups to store in a file for easier access.
        files = glob.glob(f"{backupLocation}/*.sql")
        for x in range(len(files)):
            files[x] = files[x][len(backupLocation)+1:]
        files.sort(reverse=True)
        writeFile(location + "/backups.json", files)
        # Will repair all databases and update them
        repaired, updates = database.repair()
        for x in updates:
            writeLog(f"Database updated to version {x}", 19)
        for x in repaired:
            writeLog(f"Database {x} was corrupted/missing and was restored", 9)
        # Will clean the golf games database
        deleted = database.trueSearch(f"SELECT ID FROM golfGame WHERE turnStartTime<{time.time()-86400}")
        for x in deleted:
            writeLog(f"Game #{x[0]} deleted because it is too old", 16)
        database.command(f"DELETE FROM golfGame WHERE turnStartTime<{time.time()-86400}") # Removes games that have not been touched for more than 24 hours
        database.command("DELETE FROM golfGamePlayers WHERE NOT EXISTS (SELECT * FROM golfGame WHERE golfGamePlayers.gameID = golfGame.ID)") # Removes players from games that do not exist
        database.command("DELETE FROM golfGameCards WHERE NOT EXISTS (SELECT * FROM golfGame WHERE golfGameCards.gameID = golfGame.ID)") # Removes players from games that do not exist
        writeLog("Server maintenance ran succesfully.", 12)
        # Makes sure that the vendor folder is blocked
        try:
            os.remove(location + "vendor/.htaccess")
        except:
            1
        with open(location + "vendor/.htaccess", "w") as f:
            f.write("""Order allow,deny
Deny from all""")

    # Will add to log if a library could not be connected to
    if skipGPIO:
        writeLog("Could not import GPIO library", 9)
    while True:  # will wait until connected to internet
        try:
            urllib.request.urlopen("https://google.com")
            break
        except Exception:
            try:
                urllib.request.urlopen("https://bing.com")
                break
            except Exception:
                # Skips the waiting if development machine (So you don't have to wait 2 minutes for the booting)
                if (developmentMachine):
                    break
                continue
    # Runs stuff that runs every boot
    dailyMaintenance()
    # Will turn on the internet to make sure that it is on
    if not developmentMachine:
        internetOn = router.turnOnInternet()
    else:
        internetOn = True
    # Will try to open the json location and will create a new file if the old one is gone and will store the date in it.
    try:
        info = readFile(location + "data.json")
        info.append(info[-1])
        info.append(callTime())
        info.append(callTime())
        info.append(callTime())
    except:
        info = [callTime(), callTime()]
    writeFile(location + "data.json", info)
    # Makes sure that an extra backup does not happen on boot
    lastBackup = callTime()[1]
    try:
        os.remove(location + "maintenance-mode")
    except:
        1
    fanOn = False
    writeLog("Server has finished booting procedure", 12)
    # Will update the time every minute to make sure electricity outages are reported to the minute precise and will request a check to see if the wifi status needs to be changed
    while True:
        try:
            info = readFile(location + "data.json")
            info[-1] = callTime()
        except:
            writeLog("Electricity log error", 9)
            info = [callTime(), callTime()]
        writeFile(location + "data.json", info)
        if lastBackup != callTime()[1]:
            f = open(location + "maintenance-mode", "w")
            f.close()
            try:
                # Will run the daily script
                dailyMaintenance()
            except:
                writeLog("Daily maintenance failed.", 9)
            os.remove(location + "maintenance-mode")
            lastBackup = callTime()[1]
        try:
            minimum = database.search(
                "internet", "id=(SELECT MIN(id) FROM internet)")
            if not minimum:
                database.appendValue("internet", internetOnDeafult)
                minimum = internetOnDeafult
                writeLog("No internet schedule found creating a new one", 8)
            while int(minimum[4]) < time.time():
                oldMinimum = minimum
                database.delete("internet", f"id={minimum[5]}")
                minimum = database.search(
                    "internet", "id=(SELECT MIN(id) FROM internet)"
                )
                if not minimum:
                    database.appendValue("internet", internetOnDeafult)
                    minimum = internetOnDeafult
                writeLog(
                    f"Changing internet schedule from; {oldMinimum[0]}:{oldMinimum[1]} to {oldMinimum[2]}:{oldMinimum[3]}, to {minimum[0]}:{minimum[1]} to {minimum[2]}:{minimum[3]}",
                    8,
                )
            skip = False
        except Exception:
            writeLog("Schedule could not be updated, skipped internet check", 9)
            skip = True
        try:
            if not skip:
                internetOn = internetAction(callTime(), minimum[0:4], internetOn)
        except:
            writeLog("Internet check failed", 9)
            if not developmentMachine:
                internetOn = router.turnOnInternet()
            else:
                internetOn = True
        # Will check every 2 seconds if the button is pressed and when it is show it on the led and then wait another second to verify that it is an actual press
        while True:
            time.sleep(2)
            if os.path.isfile(location + "restart.json"): # Used to restart the server
                writeLog("Server is being restarted", 12)
                os.remove(location + "restart.json")
                os.system(f"python3 {__file__} restart")
                exit()
            if os.path.isfile(location + "update.json"): # Used to update the server
                writeLog("Server is being updated.", 19)
                os.remove(location + "update.json")
                os.system(f"git --work-tree={location[:-6]} --git-dir={location[:-5]}.git reset --hard")
                os.system(f"git --work-tree={location[:-6]} --git-dir={location[:-5]}.git pull > {location}updateInfo.log")
                os.system(f"{location[:-5]}python/update.sh")
                os.system(f"composer --working-dir={location} update")
                os.system(f"chown www-data:www-data {location}updateInfo.log")
                writeLog("Server updated successfully.", 19)
            if os.path.isfile(location + "restore.json"): # Used to restore a previous version of the database
                try:
                    file = readFile(location + "restore.json")
                    f = open(location + "maintenance-mode", "w")
                    backups = readFile(location + "backups.json")
                    # Finds the latest backup if latest is specified
                    if file == "latest":
                        biggest = 0
                        for x in backups:
                            try:
                                check = int(x[:x.find("or")])
                                if check > biggest:
                                    biggest = check
                                    file = x
                            except:
                                1
                    if file not in backups:
                        writeLog(f"Could not find backup", 9)
                        raise Exception
                    f.close()
                    backupDatabase()
                    database.restore(file)
                    dailyMaintenance()
                    writeLog(f"Backup {file} was succesfully restored", 18)
                except:
                    writeLog(f"Restore for {file} failed", 9)
                #Makes sure to clean up after it is done
                try:
                    os.remove(location + "restore.json")
                except:
                    1
                try:
                    os.remove(location + "maintenance-mode")
                except:
                    1
            if not skipGPIO:
                # Checks if the fans need to turn on or off
                if temp() > configuration["fanStart"] and not fanOn:
                    GPIO.output(16, GPIO.HIGH)
                    fanOn = True
                    writeLog(f'Fan was turned on with a temprature of {temp()}', 13)
                elif temp() < configuration["fanStop"] and fanOn:
                    GPIO.output(16, GPIO.LOW)
                    fanOn = False
                    writeLog(f'Fan was turned off with a temprature of {temp()}', 13)
                if GPIO.input(10):
                    GPIO.output(8, GPIO.HIGH)
                    time.sleep(0.2)
                    GPIO.output(8, GPIO.LOW)
                    time.sleep(0.1)
                    if GPIO.input(10):
                        GPIO.output(8, GPIO.HIGH)
                        try:
                            buttonPress(internetOn)
                        except:
                            writeLog("Button press failed", 9)
                        break
            # Alternative to simulate a button press by putting button into this folder
            elif os.path.isfile(location + "button.json"):
                try:
                    buttonPress(internetOn)
                    os.remove(location + "button.json")
                except:
                    writeLog("Button press failed", 9)
            if time.time() % 60 <= 2:
                break
except Exception as e:
    try:
        with open(location + "error.log", "w") as f:
            f.write(error(e))
        f = open(location + "maintenance-mode", "w")
        f.close()
        os.system("chmod 750 -R " + location)
        os.system("chown -R www-data:www-data " + location)
        if skipGPIO:
            raise Exception
        else:
            while True:
                GPIO.output(8, GPIO.HIGH)
                time.sleep(1)
                GPIO.output(8, GPIO.LOW)
                if GPIO.input(10):
                    time.sleep(0.5)
                    if GPIO.input(10):
                        writeLog("Server is being restarted through button", 12)
                        for x in range(10):
                            GPIO.output(8, GPIO.HIGH)
                            time.sleep(0.1)
                            GPIO.output(8, GPIO.LOW)
                            time.sleep(0.1)
                        os.system(f"python3 {__file__}")
                        exit()
                time.sleep(1)
    except Exception:
        print("crash")
        while True:
            time.sleep(1)