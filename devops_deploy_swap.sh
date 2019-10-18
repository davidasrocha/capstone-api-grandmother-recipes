#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"

PROD_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-prod" -o=jsonpath={.items..spec.selector.slot})
STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

if [ -z $WORKSPACE ]
then
    WORKSPACE="$PWD"
fi

echo ""
echo "swapping environment color"
echo ""

helm upgrade $PROJECT_NAME "$WORKSPACE/helm/" --set "productionSlot=$STAGE_ENV_COLOR" --install --reuse-values

echo ""
echo "shutdown environment $PROD_ENV_COLOR"
echo ""

helm upgrade $PROJECT_NAME "$WORKSPACE/helm/" --set "$PROD_ENV_COLOR.enabled=false" --install --reuse-values

kubectl delete configmap "$PROJECT_NAME-nginx-configs-$PROD_ENV_COLOR" --ignore-not-found=true
kubectl delete deployment "$PROJECT_NAME-$PROD_ENV_COLOR" --ignore-not-found=true