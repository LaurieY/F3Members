require 'minitest/autorun'
require 'watir-webdriver'

class TestWatir < Minitest::Unit::TestCase

def setup
@b = Watir::Browser.new :chrome
@b.goto 'bit.ly/watir-webdriver-demo'
end
def teardown
@b.close()
end

def test001_watir_works
@b.text_field(id: 'entry_1000000').set 'your name'
@b.text_field(id:'entry_1000000').set 'your fred'
@b.select_list(id: 'entry_1000001').exists?
@b.select_list(id: 'entry_1000001').select 'Ruby'
@b.select_list(id: 'entry_1000001').selected? 'Ruby'
@b.button(name: 'submit').click
@b.text.include? 'Thank you'

end

end
