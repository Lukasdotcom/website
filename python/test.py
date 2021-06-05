import json, os
def readFile(location):
    with open(location) as f:
        return json.load(f)


print(readFile(__file__[:__file__.rindex("/")+1]+"config.json")["websiteRoot"]+"/")