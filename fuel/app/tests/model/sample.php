<?php
/**
 * @group app
 */
class Test_Model_Sample extends TestCase
{
    public function test_upper()
		{
			$this->assertEquals('AIUEO', Model_Sample::upper('aiueo'));			
    }

		public function test_find()
		{
			$this->assertLessThan(count(Model_Sample::find()), 0);
		}

}
