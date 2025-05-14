<?php

namespace Juggernaut\Text\Module\App\Services;

class DemographicButtonInjection
{
    public function injectDemographicButton(): void
    {
        ?>
<script>
    let navbar3 = document.querySelector('#myNavbar');
    let ele3 = document.createElement("div");
    ele3.id = "customepatientnav3";
    ele3.innerHTML = "<button class='btn btn-success  ml-2 mr-2' id='textPatient'><?php echo xlt("Text") ?></button>";
    navbar3.appendChild(ele3);

    document
        .getElementById('textPatient')
        .addEventListener('click', function (e){
            e.preventDefault();
            textPatient();
        });

    function textPatient(){
        <?php $phone = $this->getPatientNumber($_SESSION['pid']); ?>
        let phone = '<?php echo $phone ?? null; ?>';
        let title = <?php echo xlj("Initiate Conversation"); ?>;
        let url = '../../modules/custom_modules/text-messaging-app/public/index.php/individuals?phone=' + phone;

        dlgopen(url, '_blank', 800, 600, '', title);
        return false;
    }
</script>
<?php
    }

    public function getPatientNumber($pid): array|string
    {
        $sql = "SELECT phone_cell FROM patient_data WHERE pid = ?";
        $stmt = sqlQuery($sql, [$pid]);
        //sanitize number remove anything that is not a number
        $number = preg_replace('/[^0-9]/', '', $stmt['phone_cell']);
        return $number ?? '';
    }
}
