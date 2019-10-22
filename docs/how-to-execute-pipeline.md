You need to have configure some resources to execute correctly this pipeline in Jenkins.

## Requirements

* [Jenkins AWS Pipeline](https://github.com/jenkinsci/pipeline-aws-plugin)

## How to execute

First, you need to create a new credential `AWS_DEVOPS` using AWS User Credentials.

![alt jenkins credential](./images/pipeline/jenkins-credential.png)

Second, you have a S3 Bucket named `capstone-cicd-storage-eks-configs` in `us-west-2` region.

![alt s3 buckets](./images/pipeline/aws-s3-buckets.png)

Third, you must have to a Kubernetes configuration file into of the S3 Bucket. For example `capstone`.

![alt s3 bucket files](./images/pipeline/aws-s3-bucket-files.png)

Fourth, you must have to a folder named `prod` into of the S3 Bucket. Inside of this folder, you need to upload the configuration map files used by the pods.

![alt s3 bucket config maps](./images/pipeline/aws-s3-bucket-configuration-maps.png)

Finally, you need to connect your Jenkins with your SCM. After configure it, the first pipeline execution will index the branch and present for you a screen similar.

![alt jenkins pipeline first execution](./images/pipeline/jenkins-pipeline-first-execution.png)
