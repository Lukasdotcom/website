import RPi.GPIO as GPIO
import requests
from urllib.request import urlopen


def loginInternet():  # Makes sure that the server is logged in
    urlLogin = "http://192.168.1.254/cgi-bin/login.ha"
    login = {
        "nonce": "76d8f8d800112e9573bacb9c56f0ecf82c9f2ed23fa10a83",
        "password": "@<*08<**89",
        "Continue": "Continue"
    }
    loginCode = lol(urlLogin).read().decode("utf-8")
    start = loginCode.find('<input type="hidden" name="nonce" value=')
    end = loginCode.find('/>', start)
    loginCode = loginCode[start+41:end-2]
    login["nonce"] = loginCode
    login2 = requests.post(urlLogin, data=login)
    return loginCode


def turnOnInternet():  # Turns on the internet
    return True
    code = loginInternet()
    postObjectOff = {
        "nonce": code,
        "owl80211on": "on",
        "ostandard": "bgn",
        "obandwidth": "20",
        "ochannelplusauto": "0",
        "opower": "100",
        "oussidenable": "off",
        "ossidname": "ATT7MTctna",
        "ohide": "off",
        "osecurity": "defwpa",
        "owpaversion": "2",
        "owps": "on",
        "omaxclients": "80",
        "ogssidenable": "off",
        "ossidname2": "ATT7MTctna_Guest",
        "ohide2": "off",
        "osecurity2": "wpa",
        "owpaversion2": "2",
        "omaxclients2": "10",
        "owpspin": "",
        "twl80211on": "on",
        "tstandard": "ac",
        "tbandwidth": "80",
        "tchannelplusauto": "0",
        "tpower": "100",
        "tussidenable": "on",
        "tssidname": "ATT7MTctna",
        "thide": "off",
        "tsecurity": "defwpa",
        "twps": "on",
        "tmaxclients": "80",
        "twpspin": "",
        "Restore": "Restore Defaults"
    }
    urlSet = "http://192.168.1.254/cgi-bin/wconfig-adv.ha"
    turnOn = requests.post(urlSet, data=postObjectOff)
    return True


def turnOffInternet():  # Turns of the internet
    return False
    code = loginInternet()
    postObjectOn = {
        "nonce": code,
        "owl80211on": "off",
        "ostandard": "bgn",
        "obandwidth": "20",
        "ochannelplusauto": "0",
        "opower": "100",
        "oussidenable": "off",
        "ossidname": "ATT7MTctna",
        "ohide": "off",
        "osecurity": "defwpa",
        "owpaversion": "2",
        "owps": "on",
        "omaxclients": "80",
        "ogssidenable": "off",
        "ossidname2": "ATT7MTctna_Guest",
        "ohide2": "off",
        "osecurity2": "wpa",
        "owpaversion2": "2",
        "omaxclients2": "10",
        "owpspin": "",
        "twl80211on": "off",
        "tstandard": "ac",
        "tbandwidth": "80",
        "tchannelplusauto": "0",
        "tpower": "100",
        "tussidenable": "on",
        "tssidname": "ATT7MTctna",
        "thide": "off",
        "tsecurity": "defwpa",
        "twps": "on",
        "tmaxclients": "80",
        "twpspin": ""
    }
    warn = {
        "nonce": code,
        "ReturnWarned": "Continue"
    }
    urlSet = "http://192.168.1.254/cgi-bin/wconfig-adv.ha"
    urlWarn = "http://192.168.1.254/cgi-bin/wifiwarn-adv.ha"
    turnOff1 = requests.post(urlSet, data=postObjectOn)
    turnOff2 = urlopen(urlWarn).read().decode("utf-8")
    turnOff3 = requests.post(urlSet, data=warn)
    return False
