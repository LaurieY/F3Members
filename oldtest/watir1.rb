require 'watir-webdriver'
b = Watir::Browser.new
b.goto 'bit.ly/watir-webdriver-demo'
b.text_field(:id => 'entry_1000000').set 'your name'
b.text_field(id:'entry_1000000').set 'your fred'
b.select_list(id: 'entry_1000001').exists?
b.select_list(id: 'entry_1000001').select 'Ruby'
b.select_list(:id => 'entry_1').selected? 'Ruby'
b.button(:name => 'submit').click
b.text.include? 'Thank you'