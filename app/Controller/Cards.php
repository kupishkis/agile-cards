<?php
/**
 * Cards.php
 *
 * @filesource
 * @created 14-04-17
 */

namespace App\Controller;

use App\Service\Cards as CardsService;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Cards controller
 *
 * @package App\Controller
 */
class Cards implements ControllerProviderInterface
{
    /** @var  Application */
    private $app;

    /**
     * Index page.
     *
     * @return string
     */
    public function indexAction()
    {
        return $this->app['twig']->render(
            'cards/index.twig',
            [
                'form' => $this->fileForm()->createView()
            ]
        );
    }

    /**
     * Uploads file to server.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function uploadAction(Request $request)
    {
        $form = $this->fileForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $files = $request->files->get($form->getName());
            $path  = getcwd() . '/data/uploads/';
            /** @var UploadedFile $upload */
            $upload   = $files['FileUpload'];
            $now      = new \DateTime();
            $filename = $now->format('r') . ' ' . $upload->getClientOriginalName();

            if ('text/html' === $upload->getMimeType() && 'text/xml' === $upload->getClientMimeType()) {
                // Upload only if xml document received
                $upload->move($path, $filename);

                return $this->app->redirect(
                    $this->app['url_generator']->generate('print', ['file' => $filename])
                );
            }
        }

        return $this->app->redirect(
            $this->app['url_generator']->generate('home')
        );
    }

    /**
     * Reads uploaded file and prints cards from it.
     *
     * @param string $file
     * @return RedirectResponse|string
     */
    public function printAction($file)
    {
        $home = $this->app['url_generator']->generate('home');

        if (false !== strpos($file, '..')) {
            // Sorry kids
            return $this->app->redirect($home);
        }

        $file = getcwd() . '/data/uploads/' . $file;
        if (!is_readable($file)) {
            return $this->app->redirect($home);
        }

        $service = new CardsService();
        $cards   = $service->parseFile($file);

        return $this->app['twig']->render('cards/print.twig', ['cards' => $cards]);
    }

    /**
     * Generates file upload form.
     *
     * @return Form
     */
    private function fileForm()
    {
        /** @var FormBuilder $form */
        $form = $this->app['form.factory']->createBuilder('form');

        return $form
            ->add('FileUpload', 'file')
            ->getForm();
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $this->app = $app;
        /** @var ControllerCollection $index */
        $index = $app['controllers_factory'];
        $index->get('/', [$this, 'indexAction'])->bind('home');
        $index->post('/upload', [$this, 'uploadAction'])->bind('upload');
        $index->get('/print/{file}', [$this, 'printAction'])->bind('print');

        return $index;
    }
}
