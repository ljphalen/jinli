#!/usr/bin/python
import MySQLdb
import ConfigParser
config = ConfigParser.ConfigParser()
config.readfp(open("./config.ini","rb"))
fdb=config.get("fromdb","db")
tdb=config.get("todb","db")
try:
    conn=MySQLdb.connect(host=config.get("fromdb","ip"),user=config.get("fromdb","user"),passwd=config.get("fromdb","passwd"),port=int(config.get("fromdb","port")),db=fdb)
    cur=conn.cursor()
    conn_bak=MySQLdb.connect(host=config.get("todb","ip"),user=config.get("todb","user"),passwd=config.get("todb","passwd"),port=int(config.get("todb","port")),db=tdb)
    cur_bak=conn_bak.cursor()
    cur.execute('show tables')
    table=cur.fetchall()
    for t in table:
       cur.execute('SHOW COLUMNS FROM '+t[0])
       columns=cur.fetchone()
       cur.execute('select * from '+t[0])
       result=cur.fetchall()
       for r in result:
	  cur_bak.execute('select * from '+t[0]+' where '+columns[0]+' ='+str(r[0]))
	  result_bak=cur_bak.fetchone()
	  if result_bak:
		cur_bak.execute('delete from '+tdb+'.'+t[0]+' where '+columns[0]+'='+str(r[0]))
	  cur_bak.execute('insert into '+tdb+'.'+t[0]+' select * from '+fdb+'.'+t[0]+' where '+columns[0]+'='+str(r[0]))	  
    conn_bak.commit()
    cur_bak.close()
    conn_bak.close()
    cur.close()
    conn.close()
except MySQLdb.Error,e:
     print "Mysql Error %d: %s" % (e.args[0], e.args[1])
