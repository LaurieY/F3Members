  require "faker"
  require 'watir-webdriver'
  require 'minitest/autorun'
  # Faker::PhoneNumber.phone_number
# Faker::PhoneNumber.cell_phone
# Faker::Name.first_name
# Faker::Name.last_name

class MyMiniTest
  class Unit < MiniTest::Unit

    def before_suites
      # code to run before the first test
      p "Before everything"
    end

    def after_suites
      # code to run after the last test
      p "After everything"
    end
def self.test_order
    :alpha
  end  

    def _run_suites(suites, type)
      begin
        before_suites
        super(suites, type)
      ensure
        after_suites
      end
    end

    def _run_suite(suite, type)
      begin
        suite.before_suite if suite.respond_to?(:before_suite)
        super(suite, type)
      ensure
        suite.after_suite if suite.respond_to?(:after_suite)
      end
    end

  end
end

MiniTest::Unit.runner = MyMiniTest::Unit.new
class TestWatir < Minitest::Unit::TestCase
 
def self.test_order
    :alpha
  end  
  def self.before_suite
 
	p "Logging in " 
	#puts "$PROGRAM_NAME : #{$PROGRAM_NAME}"
	@@memsurname=Faker::Name.last_name
@@memforename=Faker::Name.first_name 
@@b = Watir::Browser.new :chrome

if  ARGV.length == 1 
v1 =ARGV[0]

else
v1='10'
end

#dest = '192.168.' + v1 + '.10/'
dest = 'members.lyates.com/'
p "Logging in  to "    +  dest
@@b.goto dest
 @@b.text_field(:id => 'user_id').exists?
    @@b.text_field(:id => 'user_id').set 'laurie'
  @@b.text_field(:id => 'password').set 'laurie12'
  @@b.text_field(:id => 'captcha').set 'fe7o1'
  @@b.button(:name => 'submit').click
 @@b.text_field(:id => 'entry_99').exists? 

  @@b.button(:id=>'badd').exists?
  end

  def self.after_suite
    p "bye"
	@@b.close()
  end  
def setup
p " setup"
end
def teardown
p "teardown"
#@@b.close()
end
def test_001
p "test001"
end
def test002_add_member
p "test002"
p "Adding a member and check its at the top of the list"
Watir::Wait.until {@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").exists?}
@@topmember=@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text
p " Initial top of list = "+ @@topmember
 
 
 @@b.button(:id=>'badd').click
 
 @@b.text_field(:id=>'surname').set @@memsurname
  @@b.text_field(:id=>'forename').set @@memforename
  @@b.select_list(:id=>'location').select 'Inland'
 
  @@b.a(:id=>'sData').exists?
   @@b.a(:id=>'sData').click
   assert(Watir::Wait.until {@@b.input(:id=>'addeditdel').value.eql? 'add actioned' })
   #@@b.input(:id=>'addeditdel').exists?
   
   
  # @@b.input(:id=>'addeditdel').value
    @@b.input(:id=>'addeditdel').value.eql? 'add actioned'
	p " expecting firstname "+ @@memforename
	#p @@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text
	Watir::Wait.until{@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").exists?}
	assert(Watir::Wait.until{@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text.eql? @@memforename})
	p " Waited and firstname now "+ @@memforename
	end
=begin
def test_003_edit_member
	p "Editing a member , check "+  @@memforename + "is at the top of the list"
	assert(Watir::Wait.until{@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text.eql? @@memforename})
	p " Waited and firstname now "+ @@memforename

end
=end
def test_004_filter_member
	p "Filter a member by firstname , check its somewhere in the list"
	assert(Watir::Wait.until{@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text.eql? @@memforename})
	 # @@b.text(:id, "gs_forename").clear
	  @@b.text_field(:id, "gs_forename").set @@memforename
    @@b.text_field(:id, "gs_forename").send_keys :enter
  assert(Watir::Wait.until{@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text.eql? @@memforename})
  refute_equal(@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text,@@topmember)
	p " Waited and firstname top is now "+ @@memforename
	#sleep(5)
	@@b.element(:xpath, ".//*[@id='gview_list']/div[2]/div/table/thead/tr[2]/th[2]/div/table/tbody/tr/td[3]/a").click
	#sleep(5)
	assert(Watir::Wait.until{@@b.element(xpath: ".//table[@id='list']//tr[3]/td[2]").text.eql? @@topmember})
	p " Waited and firstname 2nd row is now "+ @@topmember
end
def test_005_delete_member
assert(Watir::Wait.until{@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2]").text.eql? @@memforename})
p " deleting with no selected row"
@@b.element(:id, "bdel").click
assert(@@b.alert.exists?)

assert_match(/Please Select Row/,@@b.alert.text)
@@b.alert.close
refute(@@b.alert.exists?)
p " deleting with  selected row"
#@@b.element(:id, "eData").click
@@b.element(xpath: ".//table[@id='list']//tr[2]/td[2][@title='"+ @@memforename +"']").click
@@b.element(:id, "bdel").click
#sleep(5)
refute(@@b.alert.exists?)

assert(Watir::Wait.until{@@b.element(:xpath, ".//*[@id='DelTbl_list']/table/tbody/tr[3]/td").exists?})

#assert(Watir::Wait.until{@@b.element(:id, "eData").exists?})
assert_match(/^Do you really want delete this row[\s\S]*$/,@@b.element(:xpath, "//*[@id='DelTbl_list']/table/tbody/tr[3]/td").text)
@@b.element(:id, "dData").click
#sleep(5)
Watir::Wait.until{@@b.input(:id=>'addeditdel').value.eql? 'delete actioned'}
# assert(@@b.input(:id=>'addeditdel').value.eql? 'delete actioned',  "message delete actioned not here")
end

=begin def test_999_closedown
  @@b.close()
  end
=end
  end