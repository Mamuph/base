<?php


/**
 * Default controller entry-point
 */
class Controller_Main extends Controller
{

    /**
     * Entry point
     */
    public function actionMain()
    {

        // Add your controller entry code below this line


        // You app finish here
        Apprunner::terminate(Apprunner::EXIT_SUCCESS);

    }

}
