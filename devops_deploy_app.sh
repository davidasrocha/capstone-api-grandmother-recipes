#!/bin/bash

# setting required parameters
PROJECT_NAME="$1"
TAG_VERSION="$2"
SERVICE_TYPE="$3"

# manage service
if [ -n "$SERVICE_TYPE" ] && [ "$SERVICE_TYPE" != "" ]
then
    if [ "$SERVICE_TYPE" != "LoadBalancer" ] && [ "$SERVICE_TYPE" != "NodePort" ]
    then
        SERVICE_TYPE="NodePort"
    fi
fi

# setting environment color
PROD_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-prod" -o=jsonpath={.items..spec.selector.slot})
STAGE_ENV_COLOR=$(kubectl get services --field-selector metadata.name="$PROJECT_NAME-service-stage" -o=jsonpath={.items..spec.selector.slot})

if [ -z $WORKSPACE ]
then
    WORKSPACE="$PWD"
fi

# initialize service
if [ "$PROD_ENV_COLOR" = "" ]
then
    kubectl apply -f "$WORKSPACE/kubernetes/services/service-prod.yaml" --dry-run=true -o yaml | sed "s/NodePort/:$SERVICE_TYPE/g" | kubectl apply -f -
fi
if [ "$STAGE_ENV_COLOR" = "" ]
then
    kubectl apply -f "$WORKSPACE/kubernetes/services/service-stage.yaml" --dry-run=true -o yaml | sed "s/NodePort/:$SERVICE_TYPE/g" | kubectl apply -f -
fi

DEPLOYMENT_BLUE=$(kubectl get deployment "$PROJECT_NAME-blue" -o jsonpath='{.metadata.uid}' --ignore-not-found)
DEPLOYMENT_GREEN=$(kubectl get deployment "$PROJECT_NAME-green" -o jsonpath='{.metadata.uid}' --ignore-not-found)

# first deployment execution
if [ "$DEPLOYMENT_BLUE" = "" ] && [ "$DEPLOYMENT_GREEN" = "" ]
then
    PROD_ENV_COLOR="blue"
fi

# manage deployment
PROD_DEPLOYMENT_COLOR=$(kubectl get deployment "$PROJECT_NAME-$PROD_ENV_COLOR" -o jsonpath='{.metadata.uid}' --ignore-not-found)
if [ "$PROD_DEPLOYMENT_COLOR" != "" ]
then
    # assurance that there is a deployment to production
    STAGE_ENV_COLOR="$STAGE_ENV_COLOR"
else
    # invert color to stage environment
    if [ "$PROD_ENV_COLOR" = "blue" ]
    then
        STAGE_ENV_COLOR="green"
    else
        STAGE_ENV_COLOR="blue"
    fi
fi

echo ""
echo "deploying application"
echo ""

kubectl apply -f "$WORKSPACE/kubernetes/config-maps/$PROJECT_NAME-config-map-$STAGE_ENV_COLOR.yaml"
kubectl apply -f "$WORKSPACE/kubernetes/deployments/deployment-$STAGE_ENV_COLOR.yaml" --dry-run=true -o yaml | sed "s/:latest/:$TAG_VERSION/g" | kubectl apply -f -

echo ""
echo "waiting deployment $STAGE_ENV_COLOR"

READY=$(kubectl get deployment $PROJECT_NAME-$STAGE_ENV_COLOR -o json | jq -r '.status.conditions[] | select(.reason == "MinimumReplicasAvailable") | .status')
while [[ "$READY" != "True" ]]; do
    READY=$(kubectl get deployment $PROJECT_NAME-$STAGE_ENV_COLOR -o json | jq -r '.status.conditions[] | select(.reason == "MinimumReplicasAvailable") | .status')
    sleep 5
done

echo ""
echo "list pods"
echo ""

kubectl get pods