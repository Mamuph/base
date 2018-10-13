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

        // Attach your IPC signals handling below this line
        Hook::instance()->attach('IPC_SIGHUP', [$this, 'actionTerminateBySignal']);
        Hook::instance()->attach('IPC_SIGINT', [$this, 'actionTerminateBySignal']);


        // Add your controller entry code below this line


        // You app finish here
        Apprunner::terminate(Apprunner::EXIT_SUCCESS);

    }


    /**
     * Action received when
     * @param int $signal
     */
    public function actionTerminateBySignal(int $signal) : void
    {

        if (!ignore_user_abort())
        {
            $exit_status = Apprunner::EXIT_FAILURE;

            switch ($signal)
            {
                // SIGHUP
                case 1:
                    $exit_status = Apprunner::EXIT_HUP;
                    break;

                // SIGINT
                case 2:
                    $exit_status = Apprunner::EXIT_CTRLC;
            }

            echo "Exiting...";

            Apprunner::terminate($exit_status);
        }
    }

}