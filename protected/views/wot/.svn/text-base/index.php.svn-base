<?php if(!empty($_GET['tag'])): ?>
<h1>Posts Tagged with <i><?php echo CHtml::encode($_GET['tag']); ?></i></h1>
<?php endif; ?>

<?
 
$this->widget('ext.jqgrid.EJqGrid', 
	array(
		'name'=>'jqgrid1',
		'compression'=>'none',
		'theme'=>'redmond',
		'useNavBar'=>true,
		'useNavBar'=>'false',
			'options'=>array(
				'datatype'=>'xml',
				'url'=>'http://localhost/~metayii/yii-svn/demos/helloworld2/?r=site/list',
				'colNames'=>array('Index','Aircraft','BuiltBy'),
				'colModel'=>array(
					array('name'=>'id','index'=>'id','width'=>'55','name'=>'invdate','index'=>'invdate','width'=>90),
					array('name'=>'aircraft','index'=>'aircraft','width'=>90),
					array('name'=>'factory','index'=>'factory','width'=>100)
				),
				'rowNum'=>10,
				'rowList'=>array(10,20,30),
				'sortname'=>'id',
				'viewrecords'=>true,
				'sortorder'=>"desc",
				'caption'=>"Airplanes from XML"
			)
		)
	);
?>