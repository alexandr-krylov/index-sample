# INDEX. Sample of AVL-tree
## Download and install
```
git clone https://github.com/alexandr-krylov/index-sample.git
cd index-sample
docker-compose up -d
docker exec -it index-sample_app_1 composer install
docker exec -it index-sample_app_1 composer setup
```
## Code sniffing and testing

## Use
### Find without index
`flatfind.php <name> <value> <filename>`  
for example  
`docker exec -it index-sample_app_1 ./flatfind.php name "Adhi Kot" tests/data.json`
### Create index
`createindex.php <name> <data filename> <index filename>`  
for example  
`docker exec -it index-sample_app_1 ./createindex.php name tests/data.json indexname.json`
### Find with index
`indexfind.php <name> <value> <data filename> <index filename>`  
for example  
`docker exec -it index-sample_app_1 ./indexfind.php name "Adhi Kot" tests/data.json indexname.json`
