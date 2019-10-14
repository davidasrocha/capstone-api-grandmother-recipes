#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"
WORKSPACE="$2"
KUBECONFIG="$3"

PROD_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-prod" -o=jsonpath={.items..spec.selector.slot})
STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

# setting kubeconfig path file
if [ -n "$KUBECONFIG" ] && [ "$KUBECONFIG" != "" ]
then
    mkdir -p ".kube/"
    KUBECONFIG="--kubeconfig $KUBECONFIG"
fi

echo ""
echo "swapping environment color"
echo ""

helm $KUBECONFIG upgrade $PROJECT_NAME $WORKSPACE --set "productionSlot=$STAGE_ENV_COLOR" --install --reuse-values

echo ""
echo "shutdown environment $PROD_ENV_COLOR"
echo ""

helm $KUBECONFIG upgrade $PROJECT_NAME $WORKSPACE --set "$PROD_ENV_COLOR.enabled=false" --install --reuse-values

kubectl delete configmap "$PROJECT_NAME-nginx-configs-$PROD_ENV_COLOR" --ignore-not-found=true
kubectl delete deployment "$PROJECT_NAME-$PROD_ENV_COLOR" --ignore-not-found=true