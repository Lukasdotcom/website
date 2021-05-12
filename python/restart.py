#!/usr/bin/env python3
import router  # Custom library that has router controls
import RPi.GPIO as GPIO
import json
import time
import datetime
import os
developmentMachine = os.path.exists("/home/pi/developer-machine")
# Makes sure that the permissions are not wrong
if not developmentMachine:
    os.system("chown -R www-data:www-data /var/www/html")
    os.system("chmod 770 -R /var/www/html")
    os.system("chown -R mysql:mysql /var/lib/mysql")
    os.system("chmod 770 -R /var/lib/mysql")
    try:
        os.system("chmod 750 -R /home/pi/python")
    except:
        1
try:
    os.remove("/var/www/html/crash")
except:
    1
f = open("/var/www/html/maintenance-mode", "w")
f.close()
while True:  # Imports this library in a slow way because it is a pain and likes to not work
    try:
        import database
        break
    except:
        continue
location = "/var/www/html/"
internetOnDeafult = ["23", "0", "5", "0", "2147483000", "1"]
internet = False
# Will setup the gpio button
GPIO.setmode(GPIO.BOARD)
GPIO.setup(10, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)
GPIO.setup(8, GPIO.OUT)
try:
    def writeLog(message, kind):  # Will automatically add to the log
        database.appendValue("log", [str(kind), message, str(time.time())])
        print(message, time.time())

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
                GPIO.output(8, GPIO.HIGH)
                writeLog("Internet turning off", 6)
                if not developmentMachine:
                    status = router.turnOffInternet()
                else:
                    status = False
                writeLog("Internet turned off", 6)
                GPIO.output(8, GPIO.LOW)
        else:
            if not status:
                GPIO.output(8, GPIO.HIGH)
                writeLog("Internet turning on", 7)
                if not developmentMachine:
                    status = router.turnOnInternet()
                else:
                    status = True
                writeLog("Internet turned on", 7)
                GPIO.output(8, GPIO.LOW)
        return status

    def writeFile(location, info):  # Will write info in json format to a file
        with open(location, 'w') as f:
            json.dump(info, f)
        os.system("chown -R www-data:www-data " + location)

    def readFile(location):  # Loads the location of a certain file and returns that file if it is json
        with open(location) as f:
            return json.load(f)

    def callTime():  # Will return the time
        time = datetime.datetime.now()
        time2 = [time.strftime("%b"), time.strftime("%d"), time.strftime(
            "%Y"), time.strftime("%H"), time.strftime("%M"), time.strftime("%S"), time.strftime("%-m")]
        return time2

    def buttonPress():  # Will run this script everytime the button is pressed
        minimum = database.search(
            "internet", "id=(SELECT MIN(id) FROM internet)")
        minimum = int(minimum[5]) - 1
        writeLog("Internet Schedule changed due to button", 5)
        if minimum == 0:
            minimum = -1
        database.appendValue(
            "internet", ["0", "0", "23", "59", str(time.time()+3600), str(minimum)])

    # Will make sure that the internal clock is right for 2 minutes
    times = time.time()
    change = 0
    startTime = times
    while change < 1:
        totalTime = time.time() - startTime
        if totalTime > 120:
            writeLog("Time may be wrong; time check failed", 9)
            break
        time.sleep(0.5)
        change = time.time() - times
        times = time.time()
        # Remember to comment out
        #break
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
    # Makes sure that a backup does not happen on boot
    lastBackup = callTime()[1]
    try:
        os.remove("/var/www/html/maintenance-mode")
    except:
        1
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
            try:
                database.backUp("/var/lib/mysql", "/backup/main", False)
                database.backUp("/var/lib/mysql", "/backup/reserve", False)
            except:
                writeLog("Database backup failed", 9)
            lastBackup = callTime()[1]
        minimum = database.search(
            "internet", "id=(SELECT MIN(id) FROM internet)")
        if not minimum:
            database.appendValue("internet", internetOnDeafult)
            minimum = internetOnDeafult
            writeLog("No internet schedule found creating a new one", 8)
        while(minimum[4] < time.time()):
            oldMinimum = minimum
            database.delete("internet", f"id={minimum[5]}")
            minimum = database.search(
                "internet", "id=(SELECT MIN(id) FROM internet)")
            if not minimum:
                database.appendValue("internet", internetOnDeafult)
                minimum = internetOnDeafult
            writeLog(
                f"Changing internet schedule from; {oldMinimum[0]}:{oldMinimum[1]} to {oldMinimum[2]}:{oldMinimum[3]}, to {minimum[0]}:{minimum[1]} to {minimum[2]}:{minimum[3]}", 8)
        try:
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
            if GPIO.input(10):
                GPIO.output(8, GPIO.HIGH)
                time.sleep(.2)
                GPIO.output(8, GPIO.LOW)
                time.sleep(.1)
                if GPIO.input(10):
                    GPIO.output(8, GPIO.HIGH)
                    buttonPress()
                    break
            if time.time() % 60 <= 2:
                break
except:
    f = open("/var/www/html/maintenance-mode", "w")
    f.close()
    if not developmentMachine:
        os.system("chmod 750 -R /var/www/html")
    os.system("chown -R www-data:www-data /var/www/html")
    while True:
        GPIO.output(8, GPIO.HIGH)
        time.sleep(1)
        GPIO.output(8, GPIO.LOW)
        time.sleep(1)
