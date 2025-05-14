<?php

namespace Juggernaut\SimpleStorageSolution\Module;

use OpenEMR\Core\Kernel;
use OpenEMR\Events\PatientDocuments\PatientDocumentStoreOffsite;
use OpenEMR\Events\PatientDocuments\PatientRetrieveOffsiteDocument;
use OpenEMR\Menu\MenuEvent;
use OpenEMR\Services\Globals\GlobalsService;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
class Bootstrap
{
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;
    private ?Kernel $kernel;

    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
        $this->eventDispatcher = $dispatcher;
        $this->kernel = $kernel;
    }



    public function subscribeToEvents(): void
    {
        $this->eventDispatcher->addListener(MenuEvent::MENU_UPDATE, [$this, 'oe_module_aws_s3_add_menu_item']);
        $this->eventDispatcher->addListener(PatientDocumentStoreOffsite::REMOTE_STORAGE_LOCATION, [$this, 'uploadToS3']);
        $this->eventDispatcher->addListener(PatientRetrieveOffsiteDocument::REMOTE_DOCUMENT_LOCATION, [$this, 'retrieveFromS3']);
    }

    public function uploadToS3(PatientDocumentStoreOffsite $event): void
    {
        $data = $event->getData(); // The file data.

        $remoteFileName = "/" . $event->getRemoteFileName(); // The file name and bucket directory.
        $storeFileName = '/var/www/html/emr/sites/default/documents/temp/' . $remoteFileName;
        file_put_contents($storeFileName, $data);

        // Upload the file to S3.
        $s3Model = new S3Model();
        $putObjectInBucket = new PutObjectInBucket($s3Model);
        $sourceFile = $storeFileName;
        // Generate a unique file name.
        $ext = explode('.', $remoteFileName);
        $uniqueFileName = 'oe-' . uniqid() . '.' . end($ext);
        $remoteUniqueFileName = '/' . $uniqueFileName;
        //upload the file to s3 and set the global variable to true if successful
        $GLOBALS['documentStoredRemotely'] = $putObjectInBucket->putObjectInBucket($remoteUniqueFileName, $sourceFile);

        $nid = generate_id();
        $fsize = filesize($storeFileName);
        $catid = (int) $event->getRemoteCategory();
        $mimetype = $event->getRemoteMimeType();
        $patient_id = $event->getPatientId();
        // Update the database.
        $query = "INSERT INTO documents ( " .
            "id, type, size, date, url, mimetype, foreign_id, docdate, name" .
            " ) VALUES ( " .
            "?, 'file_url', ?, NOW(), ?, " .
            "? , ?, NOW(), ?" .
            ")";
        $displayNameToUser = substr($remoteFileName, 1);
        sqlStatement($query, array($nid, $fsize, $remoteUniqueFileName, $mimetype, $patient_id, $displayNameToUser));
        $query = "INSERT INTO categories_to_documents ( " .
            "category_id, document_id" .
            " ) VALUES ( " .
            "?, ? " .
            ")";
        sqlStatement($query, array($catid, $nid));

        //unlink($storeFileName);
        //need to push a return link into the database
    }

    public function retrieveFromS3(PatientRetrieveOffsiteDocument $event): void
    {
        $url = $event->getOpenEMRDocumentUrl();
         //if the key is empty return
        error_log("Retrieving from S3: " . $s3Key[1]);
        $s3model = new S3Model();
        $credentials = new Credentials($s3model);
        $s3Client = $credentials->authenticateCredentials();
        $getBucket = $s3model->getAllCredentials();
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $getBucket['s3_bucket'],
            'Key' => $url,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+5 minutes');
        $presignedUrl = (string) $request->getUri();
        $event->setOffsiteUrl($presignedUrl);
    }
    public function oe_module_aws_s3_add_menu_item(MenuEvent $event): MenuEvent
    {
        $menu = $event->getMenu();

        $menuItem = new stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'mod';
        $menuItem->menu_id = 'aws3';
        $menuItem->label = xlt("AWS S3");
        $menuItem->url = "/interface/modules/custom_modules/oe-simple-storage-solution/settings.php";
        $menuItem->children = [];
        $menuItem->acl_req = ["patients", "docs"];
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'modimg') {
                $item->children[] = $menuItem;
                break;
            }
        }

        $event->setMenu($menu);

        return $event;
    }
}
