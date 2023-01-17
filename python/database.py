#!/usr/bin/env python3
import mysql.connector as mysql
import os
import json
import time

def readFile(location):  # Loads the location of a certain file and returns that file if it is json
    with open(location) as f:
        return json.load(f)

def command(command): # Will just execute a sql command
    db, cursor = connect()
    cursor.execute(command)
    db.commit()
    db.close()

def trueSearch(command): # Will just execute sql command and return result 
    db, cursor = connect()
    cursor.execute(command)
    value = cursor.fetchall()
    db.close()
    return value


def connect(database=""):
    dbInfo = readFile(__file__[: __file__.rindex("/python/database.py") + 1] + "html/config.json")
    if not database:
        database = dbInfo["database"]["name"]
    try:
        db = mysql.connect(
            host="localhost",
            passwd=dbInfo["database"]["password"],
            user=dbInfo["database"]["username"],
            database=database,
        )
    except: # Used to automatically create the user and database
        path = __file__[: __file__.rindex("/") + 1]
        with open(path + "fix.sql") as f:
            text = f.read()
        text = text.replace("{username}", dbInfo["database"]["username"])
        text = text.replace("{password}", dbInfo["database"]["password"])
        text = text.replace("{database}", dbInfo["database"]["name"])
        with open(path + "fix2.sql", 'w') as f:
            f.write(text)
        os.system(f"mysql < {path}fix2.sql")
        os.remove(path + "fix2.sql")
        print("Created the user for the database", time.time())
        db = mysql.connect(
            host="localhost",
            passwd=dbInfo["database"]["password"],
            user=dbInfo["database"]["username"],
            database=database,
        )
        appendValue("log", ["9", "Created the user for the database", str(time.time())])
    cursor = db.cursor()
    return db, cursor


def deleteTable(name):  # Will delete a table in the database
    db, cursor = connect()
    command = "DROP TABLE " + name + ";"
    cursor.execute(command)
    db.commit()
    db.close()


def createTable(name, coulumns):  # Will create a table in the database
    db, cursor = connect()
    command = "CREATE TABLE " + name + " ("
    for x in coulumns:
        command += x[0]
        if x[1] == 0:
            command += " varchar(255), "
        elif x[1] == 2:
            command += " float, "
        elif x[1] == 3:
            command += " bigint, "
        elif x[1] == 4:
            command += " text, "
        elif x[1] == 5:
            command += f" int AUTO_INCREMENT, PRIMARY KEY (`{x[0]}`), "
        elif x[1] == 6:
            command += " boolean, "
        else:
            command += " int, "
    command = command[: len(command) - 2]
    command += ");"
    cursor.execute(command)
    db.commit()
    db.close()


def appendValue(table, value, coulumns=""):  # Will add a value to a table
    db, cursor = connect()
    command = "INSERT INTO " + table + " " + coulumns + " VALUES ("
    for x in value:
        command += "'" + x + "', "
    command = command[: len(command) - 2]
    command += ");"
    cursor.execute(command)
    db.commit()
    db.close()


# Will backup a database to a certain location with a name of choosing
def backUp(fileName):
    dbInfo = readFile(__file__[: __file__.rindex("/python/database.py") + 1] + "html/config.json")
    username = dbInfo["database"]["username"]
    password = dbInfo["database"]["password"]
    database = dbInfo["database"]["name"]
    location = dbInfo["database"]["backupLocation"]
    locationdata = f"{location}/{fileName}"
    if (not os.path.exists(location)):
        os.system(f"mkdir {location}")
    os.system(f"mysqldump -u {username} --password={password} --result-file={locationdata} {database}")

def restore(fileName):
    dbInfo = readFile(__file__[: __file__.rindex("/python/database.py") + 1] + "html/config.json")
    location = dbInfo["database"]["backupLocation"]
    database = dbInfo["database"]["name"]
    locationdata = f"{location}/{fileName}"
    os.system(f"mysql {database} < {locationdata}")

def search(table, where, search="*"):  # searches for value in table
    db, cursor = connect()
    cursor.execute("SELECT " + search + " FROM " + table + " WHERE " + where + ";")
    value2 = cursor.fetchall()
    db.close()
    try:
        value = value2[0]
    except:
        value = value2
    return value


def delete(table, where):  # deletes values in table
    db, cursor = connect()
    cursor.execute("DELETE FROM " + table + " WHERE " + where + ";")
    db.commit()
    db.close()


def repair():  # Repairs all tables or updates them if needed
    # Gets Infomation schema database
    dbInfo = readFile(__file__[: __file__.rindex("/python/database.py") + 1] + "html/config.json")
    db2, cursor2 = connect("INFORMATION_SCHEMA")
    updatedVersions = []
    databaseDict = {
        "information" : [["pointer", 0], ["data", 0]],
        "cookies": [["cookie", 0], ["username", 0], ["expire", 1], ["lastIP", 0]],
        "internet": [
            ["hour", 1],
            ["minute", 1],
            ["hour2", 1],
            ["minute2", 1],
            ["expire", 1],
            ["id", 1],
        ],
        "log": [["type", 1], ["message", 0], ["time", 1]],
        "logType": [["type", 1], ["name", 0], ["color", 0]],
        "privileges": [["username", 0], ["privilege", 0]],
        "users": [["username", 0], ["password", 0]],
        "requests": [["ip", 0], ["time", 1]],
        "cookieClicker": [["username", 0], ["room", 0], ["cookies", 2], ["cookiesPs", 2], ["lastUpdate", 3]],
        "localStorage" : [["username", 0], ["data", 4]],
        "space3" : [["id", 5], ["owner", 0], ["title", 0], ["description", 4], ["preferences", 4], ["likes", 1], ["downloads", 1]],
        "space3likes" : [["id", 1], ["account", 0]],
        "golfGamePlayers" : [["gameID", 1], ["multiplier", 1], ["user", 0], ["points", 1], ["orderID", 1], ["lastMode", 0], ["upToDate", 6], ["turnsSkipped", 1]],
        "golfGameCards" : [["gameID", 1], ["user", 0], ["card", 1], ["cardPlacement", 1], ["faceUp", 6]],
        "golfGame" : [["ID", 5], ["deck", 4], ["discard", 4], ["cardNumber", 1], ["flipNumber", 1], ["multiplierForFlip", 1], ["pointsToEnd", 1], ["name", 0], ["password", 0], ["players", 1], ["playersToStart", 1], ["currentPlayer", 1], ["turnStartTime", 1], ["locked", 6], ["decks", 1], ["skipTime", 1], ["timeLeft", 1], ["skipTurns", 1], ["resetPoints", 1]],
    }
    changedTables = []
    for x in databaseDict:
        name = x
        trueValues = databaseDict[x]
        compareValues = []
        for x in trueValues:
            compareValues.append(x[0])
        cursor2.execute(
            f"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='{name}' AND TABLE_SCHEMA='website'"
        )
        value2 = cursor2.fetchall()
        value = []
        for x in value2:
            value.append(x[0])
        if name == "users":
            try:
                value.remove("USER")
            except:
                1
            try:
                value.remove("CURRENT_CONNECTIONS")
            except:
                1
            try:
                value.remove("TOTAL_CONNECTIONS")
            except:
                1
        backupValue = value.copy()
        for x in value:
            try:
                compareValues.remove(x)
            except:
                break
            backupValue.remove(x)
        if backupValue or compareValues or name == "logType":
            try:
                deleteTable(name)
            except:
                1
            createTable(name, trueValues)
            if name == "logType":
                logTypes = readFile(__file__[: __file__.rindex("/python/database.py") + 1] + "html/logTypes.json")
                for x in logTypes:
                    appendValue(name, [x["type"], x["name"], x["color"]])
            else:
                if name == "privileges":
                    appendValue(name, [dbInfo["database"]["username"], "root"])
                elif name == "users":
                    appendValue(
                        name,
                        [dbInfo["database"]["username"], dbInfo["database"]["password"]],
                    )
                changedTables.append(name)
        elif name == "information": # Used to check the information table to see if the database can be updated in a better way.
            version = trueSearch("SELECT data FROM information WHERE pointer='version'")
            latest_version = "v2.4"
            if version: # Checks if the version tag still exists.
                try: # In here you can update the version to a new version
                    version = version[0][0]
                    if version == "v1.0":
                        try:
                            command("ALTER TABLE cookies ADD lastIP varchar(255)")
                        except Exception:
                            1
                        version = "v1.1"
                        updatedVersions.append("v1.1")
                    if version == "v1.1":
                        try:
                            command("ALTER TABLE cookies ADD lastIP varchar(255)")
                        except Exception:
                            1
                        createTable("docker", [["link", 0], ["action", 0], ["image", 0], ["password", 0], ["owner", 0], ["port", 1], ["ID", 0]])
                        createTable("dockerImage", [["realName", 0], ["shortName", 0]])
                        version = "v2.0"
                        updatedVersions.append("v2.0")
                    if version == "v2.0": # Adds support for multiple decks
                        command("ALTER table golfGame ADD decks int")
                        command("UPDATE golfGame SET decks='1'")
                        version = "v2.1"
                        updatedVersions.append("v2.1")
                    if version == "v2.1": # Adds support for skip time
                        command("ALTER table golfGame ADD skipTime int")
                        command("ALTER table golfGame ADD timeLeft int")
                        command("UPDATE golfGame SET skipTime='0'")
                        command("UPDATE golfGame SET timeLeft='0'")
                        version = "v2.2"
                        updatedVersions.append("v2.2")
                    if version == "v2.2": # Adds support for limited amount of turns to skip
                        command("ALTER table golfGame ADD skipTurns int")
                        command("UPDATE golfGame SET skipTurns='0'")
                        command("ALTER table golfGamePlayers ADD turnsSkipped int")
                        command("UPDATE golfGamePlayers SET turnsSkipped='0'")
                        version = "v2.3"
                        updatedVersions.append("v2.3")
                    if version == "v2.3": # Adds support for reset points when hitting a certain multiplier
                        command("ALTER table golfGame ADD resetPoints int")
                        command("UPDATE golfGame SET resetPoints='0'")
                        version = "v2.4"
                        updatedVersions.append("v2.4")
                    if version == "v2.4": # Drops support for docker
                        command("DROP TABLE docker")
                        command("DROP TABLE dockerImages")
                        command("DELETE FROM privileges WHERE privilege='docker' OR privilege='dockerAdmin'")
                        version = "v2.5"
                        updatedVersions.append("v2.5")
                    # Fixes the version if it is invalid to the latest version
                    if version != latest_version:
                        version = latest_version
                except:
                    1
                command("DELETE FROM information WHERE pointer='version'")
                command(f"INSERT INTO information VALUES('version', '{version}')")
            else:
                command(f"INSERT INTO information VALUES('version', '{latest_version}')")
                changedTables.append("information")
    db2.close()
    return changedTables, updatedVersions