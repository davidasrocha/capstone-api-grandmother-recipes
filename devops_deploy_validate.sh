#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"

DEPLOYMENT_BLUE=$(kubectl get deployment "$PROJECT_NAME-blue" -o jsonpath='{.metadata.uid}' --ignore-not-found)
DEPLOYMENT_GREEN=$(kubectl get deployment "$PROJECT_NAME-green" -o jsonpath='{.metadata.uid}' --ignore-not-found)

PROD_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-prod" -o=jsonpath={.items..spec.selector.slot})
STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

if [ -z $WORKSPACE ]
then
    WORKSPACE="$PWD"
fi

ENV=""
COLOR=""

if [ "$DEPLOYMENT_BLUE" != "" ] && [ "$DEPLOYMENT_GREEN" != "" ]
then
    # testing stage
    ENV="stage"
    COLOR="$STAGE_ENV_COLOR"

    echo ""
    echo "validating staging deployment"
    echo ""
else
    # testing prod
    # running in first deployment
    ENV="prod"
    COLOR="$PROD_ENV_COLOR"

    echo ""
    echo "validating production deployment"
    echo ""
fi

# available only in cloud providers
INGRESS=$(kubectl get service "$PROJECT_NAME-service-$ENV" -o jsonpath={.status.loadBalancer.ingress[0].hostname} --ignore-not-found)
SERVICE_TYPE=$(kubectl get service "$PROJECT_NAME-service-$ENV" -o jsonpath='{.spec.type}' --ignore-not-found)
if [ "$SERVICE_TYPE" = "LoadBalancer" ]
then
    AWS_LOADBALANCER_NAME=$(aws elb describe-load-balancers --query "LoadBalancerDescriptions[?CanonicalHostedZoneName=='$INGRESS'].LoadBalancerName" --output text)
    aws elb wait instance-in-service --load-balancer-name "$AWS_LOADBALANCER_NAME"
fi

APP_CHECK=$(curl -X GET "http://$INGRESS/api/v1/doc" -sS -o /dev/null -w "%{http_code}")

if [ "$APP_CHECK" = 200 ]
then
    echo "deployment validated application check return valid state $APP_CHECK"
    exit 0
fi

echo "deploy aborted application check return invalid state $APP_CHECK"
echo ""
echo "shutdown environment $COLOR"
echo ""

helm upgrade $PROJECT_NAME "$WORKSPACE/helm/" --set "$COLOR.enabled=false" --install --reuse-values

kubectl delete configmap "$PROJECT_NAME-nginx-configs-$COLOR" --ignore-not-found=true
kubectl delete deployment "$PROJECT_NAME-$COLOR" --ignore-not-found=true

exit 1