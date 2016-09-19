<?php



/**
 * Main test group
 */
class TestsSystemParams extends PHPUnit_Framework_TestCase
{

    private static $argv_val;


    public static function setUpBeforeClass()
    {
        global $argv;

        self::$argv_val = $argv;
    }


    /**
     * Restore the argument values after each test
     */
    function tearDown()
    {
        global $argv;

        $argv = self::$argv_val;
    }


    /**
     * Test if single parameter is accepted
     *
     * @group   mamuph.system.params
     */
    function test_params_single()
    {

        // Set arguments
        global $argv;

        // Mock arguments
        $argv[1] = '-a';
        $argv[2] = '-b';
        $argv[3] = '-c';

        // Process arguments
        Params::process(
            [
                'alpha' =>
                [
                    'short_arg'     => 'a',
                    'optional'      => true
                ],
                'beta' =>
                [
                    'short_arg'     => 'b',
                    'optional'      => true
                ]
            ]
        );

        // Validation
        $this->assertTrue(Params::get('alpha') && Params::get('beta'));

    }


    /**
     * Test if long parameters are accepted
     *
     * @group   mamuph.system.params
     */
    function test_long_parameters()
    {

        // Set arguments
        global $argv;

        // Mock arguments
        $argv[1] = '--foo';
        $argv[2] = '--bar';
        $argv[3] = '-c';

        // Process arguments
        Params::process(
            [
                'alpha' =>
                    [
                        'long_arg'     => 'foo',
                        'optional'     => true
                    ],
                'beta' =>
                    [
                        'long_arg'     => 'bar',
                        'optional'     => true
                    ]
            ]
        );

        // Validation
        $this->assertTrue(Params::get('alpha') && Params::get('beta'));

    }


    /**
     * Test if validation report is correct when a missing extra parameter is given
     *
     * @group   mamuph.system.params
     */
    public function test_missing_extra_parameter()
    {

        // Set arguments
        global $argv;

        // Mock arguments
        $argv[1] = '--foo';
        $argv[2] = 'example';

        // Process arguments
        Params::process(
            [
                'alpha' =>
                    [
                        'long_arg'     => 'foo',
                        'accept_value' => 'string',
                        'optional'     => false
                    ]
            ]
        );

        // Validation
        $this->assertTrue(Params::get('alpha') && !Params::get('beta'));

    }


    /**
     * Test parameters evaluation when values are passed
     *
     * @group   mamuph.system.params
     */
    public function test_value_parameters()
    {

        // Set arguments
        global $argv;

        // Mock arguments
        $argv[1] = '/foo/bar';
        $argv[2] = '--gamma-par=bar';

        // Process arguments
        Params::process(
            [
                'alpha' =>
                    [
                        'accept_value' => 'string',
                        'optional'     => false
                    ],
                'beta'  =>
                    [
                        'long_arg'     => 'beta',
                        'accept_value' => 'string',
                        'optional'     => false

                    ],
                'gamma-par' =>
                    [
                        'long_arg'      => 'gamma-par',
                        'accept_value'  => 'string',
                        'optional'      => 'false'
                    ]
            ]
        );


        // Validation
        $this->assertArrayHasKey('beta', Params::validate());
        $this->assertEquals(Params::get('alpha'), $argv[1] );
        $this->assertFalse (Params::get('beta')            );
        $this->assertEquals(Params::get('gamma-par'), 'bar');

    }


    /**
     * Test if multiple free arguments are evaluated
     *
     * @group   mamuph.system.params
     */
    public function test_free_parameters()
    {
        // Set arguments
        global $argv;

        // Mock arguments
        $argv[1] = 'argument1';
        $argv[2] = 'argument2';

        // Process arguments
        Params::process(
            [
                'alpha' =>
                    [
                        'accept_value'  => 'string',
                        'optional'      => false
                    ],
                'beta'  =>
                    [
                        'accept_value'  => 'string',
                        'optional'      => false
                    ]
            ]
        );

        $this->assertEquals(Params::get('alpha'), $argv[1]);
        $this->assertEquals(Params::get('beta') , $argv[2]);

    }


    public function test_set_params()
    {

        // Set arguments
        global $argv;

        // Mock arguments
        $argv[1] = 'argument1';
        $argv[2] = '--beta=argument2';

        // Process arguments
        Params::process(
            [
                'alpha' =>
                    [
                        'accept_value'  => 'string',
                        'optional'      => false
                    ],
                'beta'  =>
                    [
                        'long_arg'      => 'beta',
                        'accept_value'  => 'string',
                        'optional'      => false
                    ],
                'gamma' =>
                    [
                        'short_arg'     => 'gamma',
                        'accept_value'  => 'string',
                        'optional'      => true
                    ]
            ]
        );

        $this->assertEquals(Params::get('alpha'), $argv[1]  );
        $this->assertEquals(Params::get('beta'), 'argument2');
        $this->assertFalse (Params::get('gamma')            );

        Params::set('alpha', 'one'  );
        Params::set('beta' , 'two'  );
        Params::set('gamma', 'three');

        $this->assertEquals(Params::get('alpha'), 'one'  );
        $this->assertEquals(Params::get('beta') , 'two'  );
        $this->assertEquals(Params::get('gamma'), 'three');

    }

}