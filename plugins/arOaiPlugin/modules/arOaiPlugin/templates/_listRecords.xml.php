<?php if($recordsCount == 0):?>
  <error code="noRecordsMatch">The combination of the values of the from, until, set and metadataPrefix arguments results in an empty list.</error>
<?php else:?>
  <ListRecords>
<?php foreach($publishedRecords as $record): ?>
<?php $requestname->setAttribute('informationObject', $record) ?>
  
  <?php if($record->levelOfDescription != 'Media'): ?>
   <record>
    <header>
      <identifier><?php echo $record->getOaiIdentifier() ?></identifier>
      <datestamp><?php echo QubitOai::getDate($record->getUpdatedAt())?></datestamp>
      <setSpec><?php echo $record->getCollectionRoot()->getOaiIdentifier()?></setSpec>
    </header>
    <metadata>
      <?php echo get_component('sfDcPlugin', 'dc', array('resource' => $record)) ?>
    </metadata>

    <hierarchy>
      <atomId><?php echo $record->id ?></atomId>
      <parentId><?php echo $record->parent->id ?></parentId>
      <topParentId><?php echo $record->getTopLevelParent() ?></topParentId>
      <level><?php echo $record->getItemLevel() ?></level>
      <order><?php echo $record->lft ?></order>
      <type><?php echo $record->levelOfDescription ?></type>
    </hierarchy>

    <?php
      // Contact de la notice (institution de conservation)
      if ($repository = $record->repository) {
        $address = $repository->getPrimaryContact();
        if ($addesss) {
          echo '<contact>';
          echo '  <name>'.sfDcPlugin::formatString($record->repository->authorizedFormOfName).'</name>';
          echo '  <street>'.sfDcPlugin::formatString($address->getStreetAddress()).'</street>';
          echo '  <zipcode>'.sfDcPlugin::formatString($address->getPostalCode()).'</zipcode>';
          echo '  <city>'.sfDcPlugin::formatString($address->getCity()).'</city>';
          echo '  <country>'.$record->getRepositoryCountry().'</country>';
          echo '</contact>';
        }
      }
    ?>

    <medias>
    <?php
      // Digital objects de la notice
      if(count($objects = $record->digitalObjects)) {
          foreach($objects as $object) {
            echo '<url name="'.sfDcPlugin::formatString($object->getName()).'" type="master">'.$object->getPublicPath().'</url>';
        }
      }
      // Digital object supplémentaires (notices filles)
      if (count($medias = $record->getDescendentDigitalObjects())) {
          foreach($medias as $media) {
            if ($media->title)
              echo '<url name="'.sfDcPlugin::formatString($media->name).'" comment="'.sfDcPlugin::formatString($media->title).'">'. $media->path .'</url>';
            else
              echo '<url name="'.sfDcPlugin::formatString($media->name).'">'. $media->path .'</url>';
          }
        }
    ?>
    </medias>
    
    <?php include('_about.xml.php') ?>
   </record>
<?php endif; ?>   
<?php endforeach ?>
  <?php if($remaining > 0):?>
    <resumptionToken><?php echo $resumptionToken?></resumptionToken>
  <?php endif?>
  </ListRecords>
<?php endif?>
