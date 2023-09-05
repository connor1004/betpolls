import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Stack,
  Form, FormLayout,
  InlineError, TextField, Button, ButtonGroup, ResourceList, SkeletonBodyText, TextStyle
} from '@shopify/polaris';

class Item extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      showDeleteConfirm: false
    };
    this.formikRef = null;
    this.handleDetail = this.handleDetail.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleDetail() {
    const {
      onDetail, data
    } = this.props;
    await onDetail(data);
  }

  async handleDelete() {
    const {
      onDelete, data
    } = this.props;
    await onDelete(data.id, this);
  }

  async handleToggleActive() {
    const {
      onToggleActive, data
    } = this.props;
    await onToggleActive(data.id, this);
  }

  renderViewMode() {
    const {
      data
    } = this.props;
    const {
      showDeleteConfirm, isDeleting
    } = this.state;

    return (
      <div>
        <Card>
          <ResourceList.Item>
            {
              showDeleteConfirm ? (
                <Stack>
                  <Stack.Item fill>
                    {`Do you want to delete ${data.title}?`}
                  </Stack.Item>
                  <Stack.Item>
                    <ButtonGroup segmented>
                      <Button
                        primary
                        loading={isDeleting}
                        onClick={this.handleDelete}
                      >
                        Yes
                      </Button>
                      <Button
                        disabled={isDeleting}
                        onClick={this.toggleDeleteConfirm}
                      >
                        No
                      </Button>
                    </ButtonGroup>
                  </Stack.Item>
                </Stack>
              ) : (
                <Stack>
                  <Stack.Item>
                    <TextStyle>
                      <div style={{ width: '50px' }}>{data.id}</div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextStyle>
                      {data.title}
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextStyle>
                      {data.slug}
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item>
                    <TextStyle>
                      <div style={{ width: '100px' }}>{data.created_at}</div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item>
                    <TextStyle>
                      <div style={{ width: '100px' }}>{data.updated_at}</div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px' }}>
                      <ButtonGroup segmented>
                        <Button
                          primary
                          icon="products"
                          onClick={this.handleDetail}
                        />
                        <Button
                          icon={data.deleted_at === null ? 'cancelSmall' : 'checkmark'}
                          primary={data.deleted_at !== null}
                          destructive={data.deleted_at === null}
                          onClick={this.handleToggleActive}
                        />
                        <Button
                          destructive
                          icon="delete"
                          onClick={this.toggleDeleteConfirm}
                        />
                      </ButtonGroup>
                    </div>
                  </Stack.Item>
                </Stack>
              )
            }
          </ResourceList.Item>
        </Card>
      </div>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    if (isLoading) {
      return (
        <div>
          <Card>
            <ResourceList.Item>
              <SkeletonBodyText />
            </ResourceList.Item>
          </Card>
        </div>
      );
    }
    return this.renderViewMode();
  }
}

Item.defaultProps = {
  isLoading: false,
  onDelete: () => {},
  onDetail: () => {},
  onToggleActive: () => {}
};

export default Item;
