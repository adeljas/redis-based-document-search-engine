# redis-based-document-search-engine

========================

**Architecture**
- RediSearch https://oss.redislabs.com/redisearch/
	- I used the docker container for the ease of setting up, this is an open source full-text search engine developed by Redis Labs.
- FosRestBundle 
	- useful bundle for handling restful APIs
- ApiDoc
    - useful for generating api documentation and a sandbox

there are 3 key files: 

AppBundle\Controller\DocumentApiController
- used to handle requests and provide a restful interface
AppBundle\Entity\Document
- used to describe the Document entity, [ has an Identifier and Contents ]
AppBundle\Util\RedisDocumentHelper
- used to connect to RediSearch, issue commands with a simple and clear interface

**Usage**
- I included a Vagrant provisioning script to prepare an environment with 
    - PHP7.1
    - Apache2
    - RedisSearch ( Running on Docker )
- install VirtualBox and Vagrant then
    - run "vagrant up" within the root directory
    - navigate to http://localhost:8080 and you'll find the ApiDoc page
    
    
