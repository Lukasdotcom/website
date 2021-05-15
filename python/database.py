import mysql.connector as mysql
import os
import json

def readFile(location):  # Loads the location of a certain file and returns that file if it is json
    with open(location) as f:
        return json.load(f)


dbInfo = readFile("/var/www/html/config.json")
db = mysql.connect(host="localhost", passwd=dbInfo["database"]["password"],
                   user=dbInfo["database"]["username"], database=dbInfo["database"]["name"])
cursor = db.cursor()


def deleteTable(name):  # WIll delete a table in the database
    command = "DROP TABLE " + name + ";"
    cursor.execute(command)
    db.commit()


def createTable(name, coulumns):  # Will create a table in the database
    command = "CREATE TABLE " + name + " ("
    for x in coulumns:
        command += x[0]
        if x[1] == 0:
            command += " varchar(255), "
        else:
            command += " int, "
    command = command[:len(command) - 2]
    command += ");"
    cursor.execute(command)
    db.commit()


def appendValue(table, value, coulumns=""):  # Will add a value to a table
    command = "INSERT INTO " + table + " " + coulumns + " VALUES ("
    for x in value:
        command += "'" + x + "', "
    command = command[:len(command) - 2]
    command += ");"
    cursor.execute(command)
    db.commit()


# Will backup(restore does nothing right now) a database
def backUp(dbLocation, location, restore):
    f = open("/var/www/html/maintenance-mode", "w")
    f.close()
    os.system("sudo service mysql stop")
    command = "cp -pr "
    if restore:
        command += location
        command += " "
        command += dbLocation
    else:
        command += dbLocation
        command += " "
        command += location
    command += " -r"
    print("Check")
    os.system(command)
    os.system("sudo service mysql start")
    os.remove("/var/www/html/maintenance-mode")


def search(table, where, search="*"): # searches for value in table
    cursor.execute("SELECT " + search +
                   " FROM " + table + " WHERE " + where + ";")
    value = cursor.fetchall()[0]
    return value


def delete(table, where): # deletes values in table
    cursor.execute("DELETE FROM " + table + " WHERE " + where + ";")
    db.commit()


def repair(): # Repairs all tables
    # Gets Infomation schema database
    dbInfo = readFile("/var/www/html/config.json")
    db2 = mysql.connect(host="localhost", passwd=dbInfo["database"]["password"],
                        user=dbInfo["database"]["username"], database="INFORMATION_SCHEMA")
    cursor2 = db2.cursor()
    databaseDict = {
        "cookies": [["cookie", 0], ["username", 0], ["expire", 1]],
        "internet": [["hour", 1], ["minute", 1], ["hour2", 1], ["minute2", 1], ["expire", 1], ["id", 1]],
        "log": [["type", 1], ["message", 0], ["time", 1]],
        "logType" : [["type", 1], ["name", 0], ["color", 0]],
        "privileges" : [["username", 0], ["privilege", 0]],
        "users" : [["username", 0], ["password", 0]],
    }
    changedTables = []
    for x in databaseDict:
        name = x
        trueValues = databaseDict[x]
        compareValues = []
        for x in trueValues:
            compareValues.append(x[0])
        cursor2.execute(
            f"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='{name}'")
        value2 = cursor2.fetchall()
        value = []
        for x in value2:
            value.append(x[0])
        backupValue = value.copy()
        if name == "users":
            backupValue.remove("USER")
            backupValue.remove("CURRENT_CONNECTIONS")
            backupValue.remove("TOTAL_CONNECTIONS")
        for x in value:
            try:
                compareValues.remove(x)
            except:
                break
            backupValue.remove(x)
        if backupValue or compareValues:
            try:
                deleteTable(name)
            except:
                1
            createTable(name, trueValues)
            if name == "logType":
                logTypes = readFile("/var/www/html/logTypes.json")
                for x in logTypes:
                    appendValue(name, [x["type"], x["name"], x["color"]])
            elif name == "privileges":
                appendValue(name, [dbInfo["database"]["username"], "root"])
            elif name == "users":
                appendValue(name, [dbInfo["database"]["username"], dbInfo["database"]["password"]])
            changedTables.append(name)
    return changedTables

