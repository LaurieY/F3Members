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
 

 
  end

  def self.after_suite
    p "bye"
	#@@b.close()
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
	p "Logging in " 
	#puts "$PROGRAM_NAME : #{$PROGRAM_NAME}"
	@@memsurname=Faker::Name.last_name
@@memforename=Faker::Name.first_name 
#@@b = Watir::Browser.new :chrome
@@b = Watir::Browser.new

if  ARGV.length == 1 
v1 =ARGV[0]

else
v1='10'
end

dest = '192.168.' + v1 + '.10/'
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

  end