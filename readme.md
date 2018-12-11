Sample Management Systm


1. Requirement: 
	python >= 2.7.11
	openpyxl == 2.5.7
	openpyxl dependencies: 
		jdcal == 1.4 
		et-xmlfile == 1.0.1
	``` 
	pip install openpyxl
	```

2. Change ``$target = "uploads" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);`` to ``$target= "../../../../tmp" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);`` in sendcreatesample.php, if web portal is deployed to http://lce-test.biohpc.swmed.edu/lungcancer/. 

3. Issue: There's an issue of generating URL-encoded query string by using http_build_query() on lce-test server. 

