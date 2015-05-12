  require "faker"
  require 'watir-webdriver'
  # Faker::PhoneNumber.phone_number
# Faker::PhoneNumber.cell_phone
# Faker::Name.first_name
# Faker::Name.last_name
memsurname=Faker::Name.last_name
memforename=Faker::Name.first_name 
  b = Watir::Browser.start '192.168.10.10/', :chrome
  b.text_field(:id => 'user_id').exists?
    b.text_field(:id => 'user_id').set 'laurie'
  b.text_field(:id => 'password').set 'laurie12'
  b.text_field(:id => 'captcha').set 'fe7o1'
  b.button(:name => 'submit').click
 b.text_field(:id => 'entry_99').exists? 

  b.button(:id=>'badd').exists?
 b.button(:id=>'badd').click
 
 b.text_field(:id=>'surname').set memsurname
  b.text_field(:id=>'forename').set memforename
  b.select_list(:id=>'location').select 'Inland'
 
  b.a(:id=>'sData').exists?
   b.a(:id=>'sData').click
   Watir::Wait.until {b.input(:id=>'addeditdel').value.eql? 'add actioned' }
   #b.input(:id=>'addeditdel').exists?
   
  # b.input(:id=>'addeditdel').value
    b.input(:id=>'addeditdel').value.eql? 'add actioned'
	
 b.close
  
  