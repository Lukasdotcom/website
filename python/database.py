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
    mycursor.execute(command)
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
    os.system("sudo service mariadb stop")
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
    os.remove("/var/www/html/maintenance-mode")
    os.system(command)
    os.system("sudo service mariadb start")


def search(table, where, value="*"):
    cursor.execute("SELECT " + value +
                   " FROM " + table + " WHERE " + where + ";")
    value = cursor.fetchall()
    try:
        return value[0]
    except:
        return value

def delete(table, where):
    cursor.execute("DELETE FROM " + table + " WHERE " + where + ";")
    db.commit()

