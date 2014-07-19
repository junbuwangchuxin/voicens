create user voicens with password 'voicens123' ;

ALTER USER voicens WITH PASSWORD 'voicens123';

create database voicens with encoding='utf8' ;

grant all privileges on database voicens to voicens ;

\connect voicens;

alter database voicens owner to voicens;

alter schema public owner to voicens;

