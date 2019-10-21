#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"

DEPLOYMENT_BLUE=$(kubectl get deployment "$PROJECT_NAME-blue" -o jsonpath='{.metadata.uid}' --ignore-not-found)
DEPLOYMENT_GREEN=$(kubectl get deployment "$PROJECT_NAME-green" -o jsonpath='{.metadata.uid}' --ignore-not-found)

if [ "$DEPLOYMENT_BLUE" = "" ] || [ "$DEPLOYMENT_GREEN" = "" ]
then
    echo "Skipped swap, there is only one deployment"
    exit 0
fi

PROD_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-prod" -o=jsonpath={.items..spec.selector.slot})
STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

if [ -z $WORKSPACE ]
then
    WORKSPACE="$PWD"
fi

echo ""
echo "swapping environment color"
echo ""

kubectl patch service "$PROJECT_NAME-service-prod" -p "{\"spec\":{\"selector\":{\"slot\":\"$STAGE_ENV_COLOR\"}}}"

echo ""
echo "shutdown environment $PROD_ENV_COLOR"
echo ""

kubectl patch service "$PROJECT_NAME-service-stage" -p "{\"spec\":{\"selector\":{\"slot\":\"$PROD_ENV_COLOR\"}}}"

kubectl delete configmap "$PROJECT_NAME-config-map-$PROD_ENV_COLOR" --ignore-not-found=true
kubectl delete deployment "$PROJECT_NAME-$PROD_ENV_COLOR" --ignore-not-found=true