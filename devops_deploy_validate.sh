#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"
WORKSPACE="$2"
KUBECONFIG="$3"

STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

# setting kubeconfig path file
if [ -n "$KUBECONFIG" ] && [ "$KUBECONFIG" != "" ]
then
    mkdir -p ".kube/"
    KUBECONFIG="--kubeconfig $KUBECONFIG"
fi

APP_PORT_STAGE=$(kubectl get service "$PROJECT_NAME-service-lb" -o jsonpath={.spec.ports[0].nodePort})
APP_URL_STAGE="http://$PROJECT_NAME.local:$APP_PORT_STAGE"
APP_CHECK_STAGE_HTTP_CODE=$(curl -X GET "$APP_URL_STAGE" -sS -o /dev/null -w "%{http_code}")

if [ "$APP_CHECK_STAGE_HTTP_CODE" = 200 ]
then
    exit 0
fi

echo ""
echo "deploy aborted application check return invalid state $APP_CHECK_STAGE_HTTP_CODE"
echo ""
echo "shutdown stage environment $STAGE_ENV_COLOR"
echo ""

helm $KUBECONFIG upgrade $PROJECT_NAME $WORKSPACE --set "$STAGE_ENV_COLOR.enabled=false" --install --reuse-values

kubectl delete configmap "$PROJECT_NAME-nginx-configs-$STAGE_ENV_COLOR" --ignore-not-found=true
kubectl delete deployment "$PROJECT_NAME-$STAGE_ENV_COLOR" --ignore-not-found=true

exit 1