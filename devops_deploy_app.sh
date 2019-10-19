#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"
TAG_VERSION="$2"
SERVICE_TYPE="$3"

# setting environment color
PROD_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-prod" -o=jsonpath={.items..spec.selector.slot})
STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

if [ "$PROD_ENV_COLOR" = "" ]
then
    PROD_ENV_COLOR="blue"
else
    PROD_DEPLOYMENT_COLOR=$(kubectl get deployment "$PROJECT_NAME-$PROD_ENV_COLOR" -o jsonpath='{.metadata.uid}' --ignore-not-found)

    if [ "$PROD_DEPLOYMENT_COLOR" != "" ]
    then
        PROD_ENV_COLOR="$STAGE_ENV_COLOR"
    fi
fi

# setting tag version to nginx and php
if [ -n "$TAG_VERSION" ] && [ "$TAG_VERSION" != "" ]
then
    TAG_VERSION=",image.nginx.tag=$TAG_VERSION,image.php.tag=$TAG_VERSION"
fi

if [ -n "$SERVICE_TYPE" ] && [ "$SERVICE_TYPE" != "" ]
then
    if [ "$SERVICE_TYPE" != "LoadBalancer" ] || [ "$SERVICE_TYPE" != "NodePort" ]
    then
        SERVICE_TYPE=",service.lb.type=$SERVICE_TYPE"
    fi
fi

if [ -z $WORKSPACE ]
then
    WORKSPACE="$PWD"
fi

# executing helm to do blue-green deployment

echo ""
echo "deploying application package"
echo ""

helm upgrade $PROJECT_NAME "$WORKSPACE/helm/" --set "$PROD_ENV_COLOR.enabled=true$TAG_VERSION""$SERVICE_TYPE" --install --reuse-values --cleanup-on-fail --wait --timeout 60