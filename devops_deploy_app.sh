#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"
WORKSPACE="$2"
TAG_VERSION="$3"
KUBECONFIG="$4"
SERVICE_TYPE="$5"

# setting environment color
PROD_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-prod" -o=jsonpath={.items..spec.selector.slot})
STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

if [ "$PROD_ENV_COLOR" = "" ]
then
    PROD_ENV_COLOR="blue"
else
    PROD_ENV_COLOR="$STAGE_ENV_COLOR"
fi

# setting kubeconfig path file
if [ -n "$KUBECONFIG" ] && [ "$KUBECONFIG" != "" ]
then
    mkdir -p ".kube/"
    KUBECONFIG="--kubeconfig $KUBECONFIG"
fi

# setting tag version to nginx and php
TAG_VERSION=",image.nginx.tag=$TAG_VERSION,image.php.tag=$TAG_VERSION"

if [ -n "$SERVICE_TYPE" ] && [ "$SERVICE_TYPE" != "" ]
then
    if [ "$SERVICE_TYPE" != "LoadBalancer" ] || [ "$SERVICE_TYPE" != "NodePort" ]
    then
        SERVICE_TYPE=",service.lb.type=$SERVICE_TYPE"
    fi
fi

# executing helm to do blue-green deployment

echo ""
echo "deploying application package"
echo ""

helm $KUBECONFIG upgrade $PROJECT_NAME $WORKSPACE --set "$PROD_ENV_COLOR.enabled=true$TAG_VERSION""$SERVICE_TYPE"",imagePullSecrets=$PROJECT_NAME-docker-registry-secret" --install --reuse-values --cleanup-on-fail --wait --timeout 60