<?php


/**
 * Default controller entry-point
 */
class Controller_Main extends Controller
{

    /**
     * Controller_Main constructor.
     */
    public function __construct()
    {
        $this->term = new League\CLImate\CLImate();
    }


    /**
     * Entry point
     */
    public function action_main()
    {
    }

}
