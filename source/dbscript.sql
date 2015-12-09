create table if not exists Attribute_Weights
( attr_name VARCHAR(50) not null,
	attr_weight DECIMAL(8,4) NOT NULL
);

create TEMPORARY table curr_user
( attr_name VARCHAR(50) ,
	attr_weight DECIMAL(8,4) 
);

insert into curr_user(
select a.attr_name, a.attr_weight from user_attributes u inner join Attribute_Weights a ON a.attr_name = u.attribute);


create table if not exists temp_user_weights
(first_name DECIMAL(8,4) DEFAULT 0,
last_name DECIMAL(8,4) DEFAULT 0,
profile_picture  DECIMAL(8,4) DEFAULT 0,
gender DECIMAL(8,4) DEFAULT 0,
age DECIMAL(8,4) DEFAULT 0,
date_of_birth DECIMAL(8,4) DEFAULT 0,
email_address DECIMAL(8,4) DEFAULT 0,
phone_number DECIMAL(8,4) DEFAULT 0,
marital_status DECIMAL(8,4) DEFAULT 0,
current_location DECIMAL(8,4) DEFAULT 0,
previous_location DECIMAL(8,4) DEFAULT 0,
working_at DECIMAL(8,4) DEFAULT 0,
prev_working_at DECIMAL(8,4) DEFAULT 0,
studying_at DECIMAL(8,4) DEFAULT 0,
prev_studying_at DECIMAL(8,4) DEFAULT 0,
father_name DECIMAL(8,4) DEFAULT 0,
mother_name DECIMAL(8,4) DEFAULT 0,
mother_maiden_name DECIMAL(8,4) DEFAULT 0,
family_information DECIMAL(8,4) DEFAULT 0,
checkins DECIMAL(8,4) DEFAULT 0);

//what will come from UI
create TEMPORARY table user_attributes
(
attribute VARCHAR(50)
);
INSERT INTO user_attributes(attribute) VALUES ('first_name'),('last_name'),('profile_picture'),('gender'),('date_of_birth'),('email_address'),('current_location')

