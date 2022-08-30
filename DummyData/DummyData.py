'''
generates dummy data for COP4331 small project
usaage: python3 DummyData.py
resulting Users and Contacts insert commands are generated in users_data.txt and contacts_data.txt
aj futo, group 25
'''

from random import randint, choice
import string

NUM_USERS = 20
MAX_CONTACTS_PER_USER = 5

FIRST_NAMES = []
LAST_NAMES = []

with open("firstnames.txt") as first:
    for line in first:
        FIRST_NAMES.append(line.strip()) 

with open("lastnames.txt") as last:
    for line in last:
        LAST_NAMES.append(line.strip())

EMAIL_DOMAINS = ['gmail.com', 'ucf.edu', 'yahoo.com', 'aol.com', 'comcast.net', 'knights.ucf.edu', 'cs.ucf.edu']

USERS_INSERTS = []
CONTACTS_INSERTS = []

for i in range(NUM_USERS):
    user_first = choice(FIRST_NAMES)
    user_last = choice(LAST_NAMES)
    user_login = f'{user_first[0]}{user_last}{randint(0, 100)}'
    user_pass = ''.join(choice(string.ascii_letters) for i in range(10))
    USERS_INSERTS.append(f"insert into Users (FirstName,LastName,Login,Password) VALUES ('{user_first}','{user_last}','{user_login}','{user_pass}');")
    for j in range(MAX_CONTACTS_PER_USER):
        contact_first = choice(FIRST_NAMES)
        contact_last = choice(LAST_NAMES)
        contact_phone = randint(1000000000, 9999999999)
        contact_email = f'{contact_first[0]}{contact_last}{randint(0, 100)}@{choice(EMAIL_DOMAINS)}'
        CONTACTS_INSERTS.append(f"insert into Contacts (FirstName,LastName,PhoneNumber,Email,UserID) VALUES ('{contact_first}','{contact_last}','{contact_phone}','{contact_email}','{i+1}');")

with open("users_data.txt", "w") as ufile:
    for u in USERS_INSERTS:
        ufile.write(u+"\n")

with open("contacts_data.txt", "w") as cfile:
    for c in CONTACTS_INSERTS:
        cfile.write(c+"\n")